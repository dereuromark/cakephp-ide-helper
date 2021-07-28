<?php

namespace IdeHelper\Annotator\Template;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Throwable;

/**
 * Extracts variables from CakePHP php/ctp templates using AST.
 */
class EnhancedVariableExtractor {

	/**
	 * @param string $content
	 * @return array
	 */
	public function extract(string $content) {
		$nodes = $this->parse($content);

		$result = [];

		$nodeFinder = new NodeFinder();

		//TODO: this finds too many!

		/** @var \PhpParser\Node\Expr\Variable[] $variables */
		$variables = $nodeFinder->findInstanceOf($nodes, Node\Expr\Variable::class);

		foreach ($variables as $variable) {
			if ($variable->name === 'this') {
				continue;
			}

			$result[$variable->name] = [
				'name' => $variable->name,
			];
		}

		return $result;
	}

	/**
	 * @param string $content
	 *
	 * @return Node[]
	 */
	protected function parse(string $content): array {
		$parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
		try {
			$ast = $parser->parse($content);

			// throwing error handler
			assert($ast !== null);

			$ast = $this->resolveNames($ast);

		} catch (Throwable $exception) {
			return [];
		}

		return $ast;
	}

	/**
	 * Resolves namespaced names in the AST.
	 *
	 * @param Node[] $ast
	 *
	 * @return Node[]
	 */
	protected function resolveNames(array $ast) : array
	{
		$nodeTraverser = new NodeTraverser();

		$nameResolver = new NameResolver();
		$nodeTraverser->addVisitor($nameResolver);

		//$variableFinder= new VariableFinder();
		//$nodeTraverser->addVisitor($variableFinder);

		return $nodeTraverser->traverse($ast);
	}

}
