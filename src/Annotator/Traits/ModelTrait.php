<?php

namespace IdeHelper\Annotator\Traits;

/**
 * Handles model related things
 */
trait ModelTrait {

	/**
	 * @param string $content
	 *
	 * @return array<string>
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this->([a-z]+)\s*=\s*\$this->fetchTable\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$properties = $matches[1];
		$tables = $matches[2];
		$models = array_combine($properties, $tables);

		preg_match_all('/\b(public|protected|private) \$([a-z]+)\b/i', $content, $propertyMatches);
		$excluded = $propertyMatches[2];
		foreach ($excluded as $property) {
			if (isset($models[$property])) {
				unset($models[$property]);
			}
		}

		return $models;
	}

}
