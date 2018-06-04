<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

class ModelAwareClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {
	/**
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun($path, $content) {
		if (!preg_match('#\buse ModelAwareTrait\b#', $content)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate($path) {
		$models = $this->_getUsedModels($this->content);

		$annotations = $this->_getModelAnnotations($models, $this->content);

		return $this->_annotate($path, $this->content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _getUsedModels($content) {
		preg_match_all('/\$this-\>loadModel\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

}
