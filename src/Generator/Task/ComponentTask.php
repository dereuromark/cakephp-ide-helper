<?php

namespace IdeHelper\Generator\Task;

use Cake\Controller\Controller;
use Cake\Filesystem\Folder;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class ComponentTask implements TaskInterface {

	public const CLASS_CONTROLLER = Controller::class;

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\\' . self::CLASS_CONTROLLER . '::loadComponent(0)',
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$map = [];

		$components = $this->collectComponents();
		foreach ($components as $name => $className) {
			$map[$name] = ClassName::create($className);
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
	protected function collectComponents(): array {
		$components = [];

		$folders = array_merge(App::core('Controller/Component'), AppPath::get('Controller/Component'));
		foreach ($folders as $folder) {
			$components = $this->addComponents($components, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Controller/Component', $plugin);
			foreach ($folders as $folder) {
				$components = $this->addComponents($components, $folder, $plugin);
			}
		}

		return $components;
	}

	/**
	 * @param string[] $components
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
