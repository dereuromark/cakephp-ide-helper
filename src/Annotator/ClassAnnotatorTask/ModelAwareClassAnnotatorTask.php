<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\ModelTrait;
use IdeHelper\Utility\App;
use ReflectionClass;
use Throwable;

/**
 * Annotates table properties assigned through LocatorAwareTrait and legacy ModelAwareTrait calls.
 */
class ModelAwareClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	use ModelTrait {
		getUsedModels as getAssignedModels;
	}

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
		if (preg_match('#\buse (?:ModelAwareTrait|LocatorAwareTrait)\b#', $content)) {
			return true;
		}

		/** @var class-string|null $className */
		$className = $this->getClassName($path, $content);
		if (!$className) {
			return false;
		}

		try {
			$reflection = new ReflectionClass($className);

			return $reflection->hasMethod('fetchModel') || $reflection->hasMethod('fetchTable');
		} catch (Throwable) {
			return false;
		}
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$tables = $this->getAssignedModels($this->content);
		foreach ($this->getUsedModels($this->content) as $table) {
			[, $property] = pluginSplit($table);
			$tables[$property] ??= $table;
		}

		$annotations = [];
		foreach ($tables as $property => $table) {
			$className = App::className($table, 'Model/Table', 'Table') ?? 'Cake\\ORM\\Table';
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $property);
		}

		return $this->annotateContent($path, $this->content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return array<string>
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this-\>fetchModel\(\'([a-z.]+)\'/i', $content, $matches);
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
