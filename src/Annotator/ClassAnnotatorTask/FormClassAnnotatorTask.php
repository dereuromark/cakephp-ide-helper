<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\UsesAnnotation;

/**
 * Form classes should automatically have `@uses` annotated for method invocation.
 */
class FormClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (strpos($path, DS . 'src' . DS) === false) {
			return false;
		}

		$appNamespace = Configure::read('App.namespace') ?: 'App';
		if (!preg_match('#\buse (\w+)\\\\Form\\\\(.+)Form\b#', $content, $matches)) {
			return false;
		}

		$varName = lcfirst($matches[2]) . 'Form';
		if (!preg_match('#\$' . $varName . '->execute\(#', $content)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		preg_match('#\buse (\w+)\\\\Form\\\\(.+)Form\b#', $this->content, $matches);
		$appNamespace = $matches[1];
		$name = $matches[2] . 'Form';

		$varName = lcfirst($name);
		$rows = explode(PHP_EOL, $this->content);
		$rowToAnnotate = null;
		foreach ($rows as $i => $row) {
			if (!preg_match('#\$' . $varName . '->execute\(#', $row)) {
				continue;
			}
			$rowToAnnotate = $i + 1;

			break;
		}

		if (!$rowToAnnotate) {
			return false;
		}

		$method = $appNamespace . '\\Form\\' . $name . '::_execute()';
		$annotations = $this->buildUsesAnnotations([$method]);

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
