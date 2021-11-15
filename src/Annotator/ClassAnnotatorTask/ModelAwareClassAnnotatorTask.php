<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Throwable;

/**
 * Classes that use ModelAwareTrait should automatically have used tables - via loadModel() call - annotated.
 */
class ModelAwareClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (preg_match('#\buse ModelAwareTrait\b#', $content)) {
			return true;
		}

		$className = $this->getClassName($path, $content);
		if (!$className) {
			return false;
		}

		try {
			$object = new $className();
			if (method_exists($object, 'loadModel')) {
				return true;
			}
		} catch (Throwable $exception) {
			// Do nothing
		}

		return false;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$models = $this->getUsedModels($this->content);

		$annotations = $this->getModelAnnotations($models, $this->content);

		return $this->annotateContent($path, $this->content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return array<string>
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this-\>loadModel\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

	/**
	 * @param string $path
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function getClassName(string $path, string $content): ?string {
		preg_match('#^namespace (.+)\b#m', $content, $matches);
		if (!$matches) {
			return null;
		}

		$className = pathinfo($path, PATHINFO_FILENAME);

		return $matches[1] . '\\' . $className;
	}

}
