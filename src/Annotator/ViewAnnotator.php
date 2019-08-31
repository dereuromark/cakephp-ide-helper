<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\HelperTrait;
use IdeHelper\Utility\AppPath;

class ViewAnnotator extends AbstractAnnotator {

	use HelperTrait;

	/**
	 * @var string[]
	 */
	protected $helpers = [];

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);

		$helpers = $this->_getHelpers();
		$annotations = $this->_buildAnnotations($helpers);

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @return string[]
	 */
	protected function _getHelpers() {
		$helperArray = $this->_parseViewClass();

		$helperArray = $this->_addAppHelpers($helperArray);

		$folders = $this->_getFolders();

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
	 * @return string[]
	 */
	protected function _parseHelpersInContent($content) {
		preg_match_all('/\$this->([A-Z][A-Za-z]+)->/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$helpers = array_unique($matches[1]);

		return $helpers;
	}

	/**
	 * @return string[]
	 */
	protected function _parseViewClass() {
		$helpers = [];

		$className = App::className('App', 'Controller', 'Controller');
		/** @var \App\Controller\AppController $Controller */
		$Controller = new $className();
		/** @var \App\View\AppView $View */
		$View = $Controller->createView();

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
	 * @param string[] $helperArray
	 *
	 * @return string[]
	 */
	protected function _addAppHelpers($helperArray) {
		$paths = AppPath::get('View/Helper');
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

	/**
	 * @param string[] $helpers
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function _buildAnnotations(array $helpers) {
		$annotations = [];
		foreach ($helpers as $alias => $className) {
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $alias);
		}

		return $annotations;
	}

	/**
	 * @return string[]
	 */
	protected function _getFolders() {
		$plugin = null;
		$folders = AppPath::get('Template', $plugin);
		$plugins = Configure::read('IdeHelper.includedPlugins');
		if ($plugins === true) {
			$plugins = Plugin::loaded();
		} else {
			$plugins = (array)$plugins;
		}
		foreach ($plugins as $plugin) {
			$folders = array_merge($folders, AppPath::get('Template', $plugin));
		}

		return $folders;
	}

}
