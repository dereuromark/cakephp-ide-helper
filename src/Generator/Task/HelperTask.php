<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use IdeHelper\Utility\AppPath;

class HelperTask implements TaskInterface {

	/**
	 * @var array
	 */
	protected $aliases = [
		'\Cake\View\View::loadHelper(0)',
	];

	/**
	 * @return array
	 */
	public function collect() {
		$map = [];

		$helpers = $this->collectHelpers();
		foreach ($helpers as $name => $className) {
			$map[$name] = '\\' . $className . '::class';
		}

		$result = [];
		foreach ($this->aliases as $alias) {
			$result[$alias] = $map;
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

			$className = App::className($name, 'View/Helper', 'Helper');
			if (!$className) {
				continue;
			}

			$helpers[$name] = $className;
		}

		return $helpers;
	}

}
