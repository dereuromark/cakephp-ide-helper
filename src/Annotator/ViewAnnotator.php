<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotator\Traits\HelperTrait;

class ViewAnnotator extends AbstractAnnotator {

	use HelperTrait;

	/**
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);

		$helpers = $this->_getHelpers();
		$annotations = [];
		foreach ($helpers as $alias => $className) {
			$annotations[] = AnnotationFactory::createOrFail('@property', '\\' . $className, '$' . $alias);
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @return array
	 */
	protected function _getHelpers() {
		$helperArray = $this->_parseViewClass();

		$helperArray = $this->_addAppHelpers($helperArray);

		$plugin = null;
		$folders = App::path('Template', $plugin);

		$this->helpers = [];
		foreach ($folders as $folder) {
			$this->_checkTemplates($folder);
		}

		$helpers = array_unique($this->helpers);

		foreach ($helpers as $helper) {
			if (isset($helperArray[$helper])) {
				continue;
			}

			$className = $this->_findClassName($helper);
			if (!$className || strpos($className, 'Cake\\') === 0) {
				continue;
			}

			$helperArray[$helper] = $className;
		}

		return $helperArray;
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _checkTemplates($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		foreach ($folderContent[1] as $file) {
			$content = file_get_contents($file);
			$helpers = $this->_parseHelpersInContent($content);
			$this->helpers = array_merge($this->helpers, $helpers);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_checkTemplates($subFolder);
		}
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _parseHelpersInContent($content) {
		preg_match_all('/\$this-\>([A-Z][A-Za-z]+)-\>/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$helpers = array_unique($matches[1]);

		return $helpers;
	}

	/**
	 * @return array
	 */
	protected function _parseViewClass() {
		$helpers = [];

		$className = App::className('App', 'View', 'View');
		/** @var \App\View\AppView $View */
		$View = new $className();
		foreach ($View->helpers() as $alias => $helper) {
			$className = get_class($helper);
			if (strpos($className, 'Cake\\') === 0) {
				continue;
			}

			$helpers[$alias] = $className;
		}

		return $helpers;
	}

	/**
	 * @param array $helperArray
	 *
	 * @return array
	 */
	protected function _addAppHelpers($helperArray) {
		$paths = App::path('View/Helper');
		foreach ($paths as $path) {
			$folderContent = (new Folder($path))->read(Folder::SORT_NAME, true);
			if (empty($folderContent[1])) {
				continue;
			}

			$helpers = $folderContent[1];
			foreach ($helpers as $helper) {
				if (!preg_match('/^(.+)Helper.php$/', $helper, $matches)) {
					continue;
				}

				$helper = $matches[1];
				if (isset($helperArray[$helper])) {
					continue;
				}

				$helperArray[$helper] = Configure::read('App.namespace') . '\\View\\Helper\\' . $helper . 'Helper';
			}
		}

		return $helperArray;
	}

}
