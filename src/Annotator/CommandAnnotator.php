<?php

namespace IdeHelper\Annotator;

use RuntimeException;

class CommandAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Command' || substr($className, -7) !== 'Command') {
			return false;
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}
		$primaryModelClass = $this->getPrimaryModelClass($content);
		$usedModels = $this->getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->getModelAnnotations($usedModels, $content);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function getPrimaryModelClass(string $content): ?string {
		if (!preg_match('/\bprotected \$modelClass = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return null;
		}

		/** @var string $modelName */
		$modelName = $matches[1];

		return $modelName;
	}

	/**
	 * @param string $content
	 *
	 * @return array<string>
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this->loadModel\(\'([a-z.\/]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

}
