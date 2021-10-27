<?php

namespace IdeHelper\Generator\Task;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Filesystem\Folder;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;
use IdeHelper\ValueObject\StringName;

class ComponentTask implements TaskInterface {

	public const CLASS_CONTROLLER = Controller::class;
	public const CLASS_COMPONENT_REGISTRY = ComponentRegistry::class;

	/**
	 * @var array<string>
	 */
	protected $loadAliases = [
		'\\' . self::CLASS_CONTROLLER . '::loadComponent(0)',
	];

	/**
	 * @var array<string, int>
	 */
	protected $unloadAliases = [
		'\\' . self::CLASS_COMPONENT_REGISTRY . '::unload()' => 0,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$addMap = [];
		$removeList = [];

		$components = $this->collectComponents();
		foreach ($components as $name => $className) {
			$addMap[$name] = ClassName::create($className);
			if (strpos($name, '.') !== false) {
				[, $name] = pluginSplit($name);
			}
			$removeList[$name] = StringName::create($name);
		}

		ksort($addMap);
		ksort($removeList);

		$result = [];
		foreach ($this->loadAliases as $alias) {
			$directive = new Override($alias, $addMap);
			$result[$directive->key()] = $directive;
		}
		foreach ($this->unloadAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $removeList);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
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
	 * @param array<string> $components
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
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
