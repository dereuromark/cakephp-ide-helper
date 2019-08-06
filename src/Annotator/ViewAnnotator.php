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
	public function annotate(string $path): bool {
		$content = file_get_contents($path);

		$helpers = $this->getHelpers();
		$annotations = $this->buildAnnotations($helpers);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @return string[]
	 */
	protected function getHelpers(): array {
		$helperArray = $this->parseViewClass();

		$helperArray = $this->addAppHelpers($helperArray);

		$folders = $this->getFolders();

		$this->helpers = [];
		foreach ($folders as $folder) {
			$this->checkTemplates($folder);
		}

		$helpers = array_unique($this->helpers);

		foreach ($helpers as $helper) {
			if (isset($helperArray[$helper])) {
				continue;
			}

			$className = $this->findClassName($helper);
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
	protected function checkTemplates($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		foreach ($folderContent[1] as $file) {
			$content = file_get_contents($file);
			$helpers = $this->parseHelpersInContent($content);
			$this->helpers = array_merge($this->helpers, $helpers);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->checkTemplates($subFolder);
		}
	}

	/**
	 * @param string $content
	 *
	 * @return string[]
	 */
	protected function parseHelpersInContent(string $content): array {
		preg_match_all('/\$this-\>([A-Z][A-Za-z]+)-\>/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$helpers = array_unique($matches[1]);

		return $helpers;
	}

	/**
	 * @return string[]
	 */
	protected function parseViewClass(): array {
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
	protected function addAppHelpers(array $helperArray): array {
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
	protected function buildAnnotations(array $helpers): array {
		$annotations = [];
		foreach ($helpers as $alias => $className) {
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $alias);
		}

		return $annotations;
	}

	/**
	 * @return string[]
	 */
	protected function getFolders(): array {
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
