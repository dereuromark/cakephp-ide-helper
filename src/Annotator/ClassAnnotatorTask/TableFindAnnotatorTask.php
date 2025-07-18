<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Brick\VarExporter\Internal\ObjectExporter\ClosureExporter\PrettyPrinter;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\UsesAnnotation;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Throwable;

/**
 * Usage of find() or findOrFail() should have inline annotations added.
 */
class TableFindAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (!str_contains($path, DS . 'src' . DS)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$parser = (new ParserFactory())->createForHostVersion();
		try {
			$ast = $parser->parse($this->content);
		} catch (Throwable $e) {
			echo "Parse error: {$e->getMessage()}\n";
			exit(1);
		}

		$traverser = new NodeTraverser();

		$traverser->addVisitor(new class extends NodeVisitorAbstract {
			public function enterNode(Node $node): void {
				if ($node instanceof Assign && $node->expr instanceof Node\Expr\MethodCall) {
					$methodCall = $node->expr;

					// Check for ->first() or ->firstOrFail()
					if (
						$methodCall->name instanceof Node\Identifier &&
						in_array($methodCall->name->toString(), ['first', 'firstOrFail'])
					) {
						$method = $methodCall->name->toString();

						// Traverse back to $this->TableName->find()
						$callChain = $methodCall->var;
						while ($callChain instanceof Node\Expr\MethodCall) {
							$callChain = $callChain->var;
						}

						if (
							$callChain instanceof Node\Expr\PropertyFetch &&
							$callChain->var instanceof Node\Expr\Variable &&
							$callChain->var->name === 'this' &&
							$callChain->name instanceof Node\Identifier
						) {
							$tableName = $callChain->name->toString(); // e.g., "Residents"
							$varName = is_string($node->var->name) ? $node->var->name : 'unknown';

							$entityClass = '\\App\\Model\\Entity\\' . rtrim($tableName, 's'); // crude singular
							$nullable = $method === 'first' ? '|null' : '';
							$doc = new Doc("/** @var {$entityClass}{$nullable} \${$varName} */");

							$node->setDocComment($doc);
						}
					}
				}
			}
		});

		$modifiedAst = $traverser->traverse($ast);
		$prettyPrinter = new PrettyPrinter();
		$modifiedCode = $prettyPrinter->prettyPrintFile($modifiedAst);

		dd($modifiedCode);

		return $this->annotateInlineContent($path, $this->content, $annotations, $rowToAnnotate);
	}

	/**
	 * @param array<string> $classes
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildUsesAnnotations(array $classes): array {
		$annotations = [];

		foreach ($classes as $className) {
			$annotations[] = AnnotationFactory::createOrFail(UsesAnnotation::TAG, '\\' . $className);
		}

		return $annotations;
	}

}
