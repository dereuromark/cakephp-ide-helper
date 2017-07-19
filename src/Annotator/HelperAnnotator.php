<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\View\View;
use Exception;
use IdeHelper\Annotator\Traits\HelperTrait;

class HelperAnnotator extends AbstractAnnotator {

	use HelperTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if (substr($name, -6) !== 'Helper') {
			return false;
		}

		$name = substr($name, 0, -6);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'View/Helper', 'Helper');
		if (!$className) {
			return false;
		}

		try {
			$helper = new $className(new View());
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping helper annotations: ' . $e->getMessage());
			}
			return false;
		}

		$helperMap = $this->_invokeProperty($helper, '_helperMap');

		$content = file_get_contents($path);
		$annotations = [];

		$helperAnnotations = $this->_getHelperAnnotations($helperMap);
		foreach ($helperAnnotations as $helperAnnotation) {
			$regexAnnotation = str_replace('\$', '[\$]?', preg_quote($helperAnnotation));
			if (preg_match('/' . $regexAnnotation . '/', $content)) {
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

}
