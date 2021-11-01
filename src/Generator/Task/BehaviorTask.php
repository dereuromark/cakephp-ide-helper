<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;

class BehaviorTask implements TaskInterface {

	public const CLASS_TABLE = Table::class;

	/**
	 * @var array<int>
	 */
	protected $addAliases = [
		'\\' . self::CLASS_TABLE . '::addBehavior()' => 0,
	];

	/**
	 * @var array<int>
	 */
	protected $removeAliases = [
		'\\' . self::CLASS_TABLE . '::removeBehavior()' => 0,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$addList = $removeList = [];
		$behaviors = $this->collectBehaviors();
		foreach ($behaviors as $name => $className) {
			$addList[$name] = StringName::create($name);
			if (strpos($name, '.') !== false) {
				[, $name] = pluginSplit($name);
			}
			$removeList[$name] = StringName::create($name);
		}

		ksort($addList);
		ksort($removeList);

		$result = [];
		foreach ($this->addAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $addList);
			$result[$directive->key()] = $directive;
		}
		foreach ($this->removeAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $removeList);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectBehaviors(): array {
		$behaviors = [];

		$folders = array_merge(App::core('ORM/Behavior'), AppPath::get('Model/Behavior'));
		foreach ($folders as $folder) {
			$behaviors = $this->addBehaviors($behaviors, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Model/Behavior', $plugin);
			foreach ($folders as $folder) {
				$behaviors = $this->addBehaviors($behaviors, $folder, $plugin);
			}
		}

		ksort($behaviors);

		return $behaviors;
	}

	/**
	 * @param array<string, string> $behaviors
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string, string>
	 */
	protected function addBehaviors(array $behaviors, string $folder, ?string $plugin = null): array {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		// This suffices as the return value is $this (calling Table class) anyway for chaining.
		$className = Table::class;

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Behavior\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			/** @var string $name */
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$behaviors[$name] = $className;
		}

		return $behaviors;
	}

}
