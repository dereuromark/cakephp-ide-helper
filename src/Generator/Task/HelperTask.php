<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Filesystem\Folder;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;

class HelperTask implements TaskInterface {

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Cake\View\View::loadHelper(0)',
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect() {
		$map = [];

		$helpers = $this->collectHelpers();
		foreach ($helpers as $name => $className) {
			$map[$name] = '\\' . $className . '::class';
		}

		ksort($map);

		$result = [];
		foreach ($this->aliases as $alias) {
			$directive = new Override($alias, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectHelpers() {
		$helpers = [];

		$folders = array_merge(App::core('View/Helper'), AppPath::get('View/Helper'));
		foreach ($folders as $folder) {
			$helpers = $this->addHelpers($helpers, $folder);
		}

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('View/Helper', $plugin);
			foreach ($folders as $folder) {
				$helpers = $this->addHelpers($helpers, $folder, $plugin);
			}
		}

		return $helpers;
	}

	/**
	 * @param array $helpers
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return string[]
	 */
	protected function addHelpers(array $helpers, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Helper\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			try {
				$className = App::className($name, 'View/Helper', 'Helper');
			} catch (\Exception $e) {
				continue;
			} catch (\Throwable $e) {
				continue;
			}
			if (!$className) {
				continue;
			}

			$helpers[$name] = $className;
		}

		return $helpers;
	}

}
