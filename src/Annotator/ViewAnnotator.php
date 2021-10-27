<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\HelperTrait;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use RuntimeException;

class ViewAnnotator extends AbstractAnnotator {

	use HelperTrait;

	/**
	 * @var array<string>
	 */
	protected $helpers = [];

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$helpers = $this->getHelpers();
		$annotations = $this->buildAnnotations($helpers);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @return array<string>
	 */
	protected function getHelpers(): array {
		$helperArray = $this->parseViewClass();

		$helperArray = $this->addAppHelpers($helperArray);
		$helperArray = $this->addExtractedHelpers($helperArray);

		return $helperArray;
	}

	/**
	 * @param array<string, string> $helperArray
	 * @return array<string, string>
	 */
	protected function addExtractedHelpers(array $helperArray) {
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
			if ($content === false) {
				throw new RuntimeException('Cannot read file');
			}
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
	 * @return array<string>
	 */
	protected function parseHelpersInContent(string $content) {
		preg_match_all('/\$this->([A-Z][A-Za-z]+)->/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$helpers = array_unique($matches[1]);

		return $helpers;
	}

	/**
	 * @return array<string>
	 */
	protected function parseViewClass(): array {
		$helpers = [];

		/** @phpstan-var class-string<object> $className */
		$className = App::classNameOrFail('App', 'Controller', 'Controller');
		if ($this->_isAbstract($className)) {
			return [];
		}

		/** @var \App\Controller\AppController $Controller */
		$Controller = new $className();
		/** @var \Cake\View\View $View */
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
	 * @param array<string> $helperArray
	 *
	 * @return array<string>
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
	 * @param array<string> $helpers
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(array $helpers): array {
		$annotations = [];
		foreach ($helpers as $alias => $className) {
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $alias);
		}

		return $annotations;
	}

	/**
	 * @return array<string>
	 */
	protected function getFolders(): array {
		$plugin = null;
		$folders = App::path('templates', $plugin);
		$plugins = Configure::read('IdeHelper.includedPlugins');
		if ($plugins === true) {
			$plugins = Plugin::loaded(); // We cannot use all() here
		} else {
			$plugins = (array)$plugins;
		}
		foreach ($plugins as $plugin) {
			$folders = array_merge($folders, App::path('templates', $plugin));
		}

		return $folders;
	}

}
