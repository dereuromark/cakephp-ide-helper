<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\ORM\Table;

class BehaviorTask implements TaskInterface {

	const CLASS_TABLE = Table::class;

	/**
	 * @var array
	 */
	protected $aliases = [
		'\\' . self::CLASS_TABLE . '::addBehavior(0)',
	];

	/**
	 * @return array
	 */
	public function collect() {
		$map = [];

		$behaviors = $this->collectBehaviors();
		foreach ($behaviors as $name => $className) {
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
	protected function collectBehaviors() {
		$components = [];

		$folders = array_merge(App::core('ORM/Behavior'), App::path('Model/Behavior'));
		foreach ($folders as $folder) {
			$components = $this->addBehaviors($components, $folder);
		}

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$folders = App::path('Model/Behavior', $plugin);
			foreach ($folders as $folder) {
				$components = $this->addBehaviors($components, $folder, $plugin);
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
	protected function addBehaviors(array $components, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		// This suffices as the return value is $this (calling Table class) anyway for chaining.
		$className = Table::class;

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Behavior\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$components[$name] = $className;
		}

		return $components;
	}

}
