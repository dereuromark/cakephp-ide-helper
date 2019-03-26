<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use IdeHelper\Utility\AppPath;

class ComponentTask implements TaskInterface {

	/**
	 * @var array
	 */
	protected $aliases = [
		'\Cake\Controller\Controller::loadComponent(0)',
	];

	/**
	 * @return array
	 */
	public function collect() {
		$map = [];

		$components = $this->collectComponents();
		foreach ($components as $name => $className) {
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
	protected function collectComponents() {
		$components = [];

		$folders = array_merge(App::core('Controller/Component'), AppPath::get('Controller/Component'));
		foreach ($folders as $folder) {
			$components = $this->addComponents($components, $folder);
		}

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Controller/Component', $plugin);
			foreach ($folders as $folder) {
				$components = $this->addComponents($components, $folder, $plugin);
			}
		}

		return $components;
	}

	/**
	 * @param array $components
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return string[]
	 */
	protected function addComponents(array $components, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Component\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'Controller/Component', 'Component');
			if (!$className) {
				continue;
			}

			$components[$name] = $className;
		}

		return $components;
	}

}
