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
	 * @var int[]
	 */
	protected $aliases = [
		'\\' . self::CLASS_TABLE . '::addBehavior()' => 0,
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$list = [];

		$behaviors = $this->collectBehaviors();
		foreach ($behaviors as $name => $className) {
			$list[$name] = StringName::create($name);
		}

		ksort($list);

		$result = [];
		foreach ($this->aliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $list);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
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
	 * @param array $behaviors
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return string[]
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
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$behaviors[$name] = $className;
		}

		return $behaviors;
	}

}
