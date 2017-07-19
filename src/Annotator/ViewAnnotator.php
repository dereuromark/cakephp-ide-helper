<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Filesystem\Folder;
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
		$annotations = [];

		$helperAnnotations = $this->_getHelperAnnotations();
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
	 * @return array
	 */
	protected function _getHelperAnnotations() {
		$plugin = null;
		$folders = App::path('Template', $plugin);

		$this->helpers = [];
		foreach ($folders as $folder) {
			$this->_checkTemplates($folder);
		}

		$helpers = array_unique($this->helpers);

		$helperAnnotations = [];
		foreach ($helpers as $helper) {
			$className = $this->_findClassName($helper);
			if (!$className || strpos($className, 'Cake\\') === 0) {
				continue;
			}

			$helperAnnotations[] = '@property \\' . $className . ' $' . $helper;
		}

		return $helperAnnotations;
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _checkTemplates($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		foreach ($folderContent[1] as $file) {
			$content = file_get_contents($file);
			$helpers = $this->_parseHelpers($content);
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
	protected function _parseHelpers($content) {
		preg_match_all('/\$this-\>([A-Z][A-Za-z]+)-\>/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$helpers = array_unique($matches[1]);

		return $helpers;
	}

}
