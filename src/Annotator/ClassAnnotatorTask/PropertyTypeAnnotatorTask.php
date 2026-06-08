<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;

/**
 * Declared properties can be annotated with configured inline `@var` types.
 */
class PropertyTypeAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

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

		$propertyTypeMap = $this->propertyTypeMap();
		if (!$propertyTypeMap) {
			return false;
		}

		foreach (array_keys($propertyTypeMap) as $property) {
			if (preg_match('#\$\s*' . preg_quote((string)$property, '#') . '\b#', $content)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$propertyTypeMap = $this->propertyTypeMap();
		$properties = $this->findProperties($this->content, $propertyTypeMap);
		if (!$properties) {
			return false;
		}

		$changed = false;
		foreach ($properties as $property => $line) {
			$annotation = AnnotationFactory::createOrFail(VariableAnnotation::TAG, $propertyTypeMap[$property], '$' . $property);
			if ($this->annotateInlineContent($path, $this->content, [$annotation], $line)) {
				$changed = true;
			}
		}

		return $changed;
	}

	/**
	 * @param string $content
	 * @param array<string, string> $propertyTypeMap
	 * @return array<string, int>
	 */
	protected function findProperties(string $content, array $propertyTypeMap): array {
		$properties = [];
		$lines = explode("\n", $content);
		foreach ($lines as $index => $line) {
			foreach (array_keys($propertyTypeMap) as $property) {
				if (!preg_match('#^\s*(?:public|protected|private)\s+(?:static\s+)?(?:readonly\s+)?(?:[^$;=]+\s+)?\$' . preg_quote((string)$property, '#') . '\b#', $line)) {
					continue;
				}

				$properties[(string)$property] = $index + 1;
			}
		}

		return array_reverse($properties);
	}

	/**
	 * @return array<string, string>
	 */
	protected function propertyTypeMap(): array {
		/** @var array<string, string> $propertyTypeMap */
		$propertyTypeMap = (array)Configure::read('IdeHelper.propertyTypeMap');

		return array_filter($propertyTypeMap);
	}

}
