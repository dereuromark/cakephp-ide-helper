<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Utility\Inflector;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\UsesAnnotation;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
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
			$tokens = $parser->getTokens();
			$originalAst = $ast;
		} catch (Throwable $e) {
			trigger_error($e);

			return false;
		}

		$array = [];

		$traverser = new NodeTraverser();
		$traverser->addVisitor(new class($array) extends NodeVisitorAbstract {
			private array $array;

			/**
			 * @param array $array
			 */
			public function __construct(array &$array) {
				$this->array = &$array;
			}

			/**
			 * @param \PhpParser\Node $node
			 * @return \PhpParser\Node|null
			 */
			public function enterNode(Node $node): ?Node {
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
							$varName = ($node->var instanceof Node\Expr\Variable && is_string($node->var->name))
								                ? $node->var->name
							                : 'unknown';

							$entityName = Inflector::singularize($tableName);
							$entityClass = '\\App\\Model\\Entity\\' . $entityName;
							$nullable = $method === 'first' ? '|null' : '';
							$doc = new Doc("/** @var {$entityClass}{$nullable} \${$varName} */");
							$node->setDocComment($doc);

							$this->array[] = [
								'line' => $node->getStartLine(),
								'content' => $doc->getText(),
								'varName' => $varName,
								'tableName' => $tableName,
								'entityName' => $entityName,
							];
						}
					}
				}

				return null;
			}
		});

		$modifiedAst = $traverser->traverse($ast);
		$printer = new Standard();
		$modifiedCode = $printer->printFormatPreserving(
			$modifiedAst,
			$originalAst,
			$tokens,
		);

		debug($array);
		dd($modifiedCode);

		//return $this->annotateInlineContent($path, $this->content, $annotations, $rowToAnnotate);
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
