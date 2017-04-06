<?php
namespace IdeHelper\Annotator;

class ShellAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Shell' || substr($className, -5) !== 'Shell' && substr($className, -4) !== 'Task') {
			return false;
		}

		$content = file_get_contents($path);
		$primaryModelClass = $this->_getPrimaryModelClass($content);
		$usedModels = $this->_getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function _getPrimaryModelClass($content) {
		if (!preg_match('/\bpublic \$modelClass = \'([a-z.]+)\'/i', $content, $matches)) {
			return null;
		}

		$modelName = $matches[1];

		return $modelName;
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _getUsedModels($content) {
		preg_match_all('/\$this-\>loadModel\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches)) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

}
