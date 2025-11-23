<?php

namespace IdeHelper\Illuminator\Task;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

/**
 * Adds @link annotations above validator->add() calls that reference table provider methods.
 *
 * When validation rules use 'provider' => 'table' with a method reference,
 * this task adds a @link annotation to help IDEs recognize the method usage.
 */
class TableValidationLinkTask extends AbstractTask {

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);

		return $className !== 'Table' && str_ends_with($className, 'Table');
	}

	/**
	 * @param string $content
	 * @param string $path Path to file.
	 * @return string
	 */
	public function run(string $content, string $path): string {
		$links = $this->findTableProviderMethods($content);
		if (!$links) {
			return $content;
		}

		return $this->insertLinkAnnotations($content, $links);
	}

	/**
	 * Find all ->add() calls with 'provider' => 'table' and extract the method names.
	 *
	 * @param string $content
	 * @return array<int, string> Map of line number => method name
	 */
	protected function findTableProviderMethods(string $content): array {
		$parser = (new ParserFactory())->createForNewestSupportedVersion();

		try {
			$ast = $parser->parse($content);
		} catch (\Throwable $e) {
			return [];
		}

		if ($ast === null) {
			return [];
		}

		$visitor = new class extends NodeVisitorAbstract {
			/**
			 * @var array<int, string>
			 */
			public array $links = [];

			/**
			 * @param \PhpParser\Node $node
			 * @return int|null
			 */
			public function enterNode(Node $node): ?int {
				if (!$node instanceof Node\Expr\MethodCall) {
					return null;
				}

				if (!$node->name instanceof Node\Identifier || $node->name->name !== 'add') {
					return null;
				}

				// Need at least 3 arguments: field, ruleName, options array
				if (count($node->args) < 3) {
					return null;
				}

				$optionsArg = $node->args[2];
				if (!$optionsArg instanceof Node\Arg) {
					return null;
				}

				$options = $optionsArg->value;
				if (!$options instanceof Node\Expr\Array_) {
					return null;
				}

				$methodName = $this->extractTableProviderMethod($options);
				if ($methodName !== null) {
					$this->links[$node->getStartLine()] = $methodName;
				}

				return null;
			}

			/**
			 * @param \PhpParser\Node\Expr\Array_ $options
			 * @return string|null
			 */
			protected function extractTableProviderMethod(Node\Expr\Array_ $options): ?string {
				$hasTableProvider = false;
				$methodName = null;

				foreach ($options->items as $item) {
					if (!$item instanceof Node\Expr\ArrayItem || $item->key === null) {
						continue;
					}

					$key = $this->getStringValue($item->key);
					if ($key === null) {
						continue;
					}

					if ($key === 'provider') {
						$value = $this->getStringValue($item->value);
						if ($value === 'table') {
							$hasTableProvider = true;
						}
					}

					if ($key === 'rule') {
						$methodName = $this->extractMethodFromRule($item->value);
					}
				}

				if ($hasTableProvider && $methodName !== null) {
					return $methodName;
				}

				return null;
			}

			/**
			 * @param \PhpParser\Node\Expr $value
			 * @return string|null
			 */
			protected function extractMethodFromRule(Node\Expr $value): ?string {
				// 'rule' => 'methodName'
				$stringValue = $this->getStringValue($value);
				if ($stringValue !== null) {
					return $stringValue;
				}

				// 'rule' => ['methodName', 'arg1', ...]
				if ($value instanceof Node\Expr\Array_ && count($value->items) > 0) {
					$firstItem = $value->items[0];
					if ($firstItem instanceof Node\Expr\ArrayItem) {
						return $this->getStringValue($firstItem->value);
					}
				}

				return null;
			}

			/**
			 * @param \PhpParser\Node\Expr $expr
			 * @return string|null
			 */
			protected function getStringValue(Node\Expr $expr): ?string {
				if ($expr instanceof Node\Scalar\String_) {
					return $expr->value;
				}

				return null;
			}
		};

		$traverser = new NodeTraverser();
		$traverser->addVisitor($visitor);
		$traverser->traverse($ast);

		return $visitor->links;
	}

	/**
	 * Insert @link annotations above the specified lines.
	 *
	 * @param string $content
	 * @param array<int, string> $links Map of line number => method name
	 * @return string
	 */
	protected function insertLinkAnnotations(string $content, array $links): string {
		$lines = explode("\n", $content);

		// Sort by line number descending to avoid offset issues when inserting
		krsort($links);

		foreach ($links as $lineNumber => $methodName) {
			$index = $lineNumber - 1; // Convert to 0-based index
			if (!isset($lines[$index])) {
				continue;
			}

			// Check if there's already a @link annotation for this method
			if ($index > 0 && $this->hasLinkAnnotation($lines[$index - 1], $methodName)) {
				continue;
			}

			// Get the indentation of the target line
			$targetLine = $lines[$index];
			preg_match('/^(\s*)/', $targetLine, $matches);
			$indent = $matches[1] ?? '';

			// Insert the @link annotation
			$annotation = $indent . '/** @link ' . $methodName . '() */';
			array_splice($lines, $index, 0, [$annotation]);
		}

		return implode("\n", $lines);
	}

	/**
	 * Check if a line already contains a @link annotation for the given method.
	 *
	 * @param string $line
	 * @param string $methodName
	 * @return bool
	 */
	protected function hasLinkAnnotation(string $line, string $methodName): bool {
		return str_contains($line, '@link') && str_contains($line, $methodName);
	}

}
