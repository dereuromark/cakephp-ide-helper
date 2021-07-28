<?php

namespace IdeHelper\Annotator\Template;

use PhpParser\Node;
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
	public function extract($content) {
		$vars = $this->collect($content);

		dd($vars);
		//TODO: Only one InlineHTML node is visible here, how to find the vars?

		return $result;
	}

	/**
	 * @param string $content
	 *
	 * @return Node[]
	 */
	protected function collect(string $content): array {
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
		$nameResolver = new NameResolver();
		$nodeTraverser = new NodeTraverser();
		$nodeTraverser->addVisitor($nameResolver);

		return $nodeTraverser->traverse($ast);
	}

}
