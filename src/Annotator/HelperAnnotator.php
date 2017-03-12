<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
use IdeHelper\Console\Io;

/**
 */
class HelperAnnotator extends AbstractAnnotator {

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array $config
	 */
	public function __construct(Io $io, array $config) {
		parent::__construct($io, $config);
	}

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);
		$annotations = [];

		$name = pathinfo($path, PATHINFO_FILENAME);
		$name = substr($name, 0, -6);
		$className = App::className($name, 'View/Helper', 'Helper');
		$helper = new $className(new View());

		$helperMap = $this->invokeProperty($helper, '_helperMap');

		$helperAnnotations = $this->_getHelperAnnotations($helperMap);
		foreach ($helperAnnotations as $helperAnnotation) {
			if (preg_match('/' . preg_quote($helperAnnotation) . '/', $content)) {
				continue;
			}

			$annotations[] = $helperAnnotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param array $helperMap
	 * @return array
	 */
	protected function _getHelperAnnotations($helperMap) {
		if (empty($helperMap)) {
			return [];
		}

		$helperAnnotations = [];
		foreach ($helperMap as $helper => $config) {
			$className = $this->_findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$helperAnnotations[] = '@property \\' . $className . ' $' . $helper;
		}

		return $helperAnnotations;
	}

	/**
	 * @param string $helper
	 *
	 * @return string|null
	 */
	protected function _findClassName($helper) {
		$plugins = Plugin::loaded();
		if (class_exists($helper)) {
			return $helper;
		}

		$className = App::className($helper, 'View/Helper', 'Helper');
		if ($className) {
			return $className;
		}

		foreach ($plugins as $plugin) {
			$className = App::className($plugin . '.' . $helper, 'View/Helper', 'Helper');
			if ($className) {
				return $className;
			}
		}

		return null;
	}

}
