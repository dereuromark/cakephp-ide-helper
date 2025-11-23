<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Throwable;

/**
 * Usage of first() or firstOrFail() on table finders should have inline @var annotations added.
 *
 * Detects patterns like:
 * - $entity = $this->TableName->find()->first();
 * - $entity = $this->TableName->find()->firstOrFail();
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

		if (!preg_match('#->(first|firstOrFail)\(\)#', $content)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$findings = $this->findTableFinderCalls();
		if (!$findings) {
			return false;
		}

		// Process from bottom to top to avoid line number shifts
		usort($findings, fn ($a, $b) => $b['line'] <=> $a['line']);

		$annotated = false;
		foreach ($findings as $finding) {
			$annotation = $this->buildVarAnnotation($finding['entityClass'], $finding['varName'], $finding['nullable']);
			$result = $this->annotateInlineContent($path, $this->content, [$annotation], $finding['line']);
			if ($result) {
				$annotated = true;
			}
		}

		return $annotated;
	}

	/**
	 * Find all table finder calls that end with first() or firstOrFail().
	 *
	 * @return array<array{line: int, varName: string, tableName: string, entityClass: string, nullable: bool}>
	 */
	protected function findTableFinderCalls(): array {
		$parser = (new ParserFactory())->createForHostVersion();
		try {
			$ast = $parser->parse($this->content);
		} catch (Throwable $e) {
			return [];
		}

		if ($ast === null) {
			return [];
		}

		$appNamespace = Configure::read('App.namespace') ?: 'App';

		$visitor = new TableFindNodeVisitor($appNamespace);
		$traverser = new NodeTraverser();
		$traverser->addVisitor($visitor);
		$traverser->traverse($ast);

		return $visitor->getFindings();
	}

	/**
	 * Build a @var annotation for the entity type.
	 *
	 * @param string $entityClass
	 * @param string $varName
	 * @param bool $nullable
	 * @return \IdeHelper\Annotation\VariableAnnotation
	 */
	protected function buildVarAnnotation(string $entityClass, string $varName, bool $nullable): VariableAnnotation {
		$type = $entityClass . ($nullable ? '|null' : '');

		/** @var \IdeHelper\Annotation\VariableAnnotation */
		return AnnotationFactory::createOrFail(VariableAnnotation::TAG, $type, '$' . $varName);
	}

}
