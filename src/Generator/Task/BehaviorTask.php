<?php

namespace IdeHelper\Generator\Task;

use Cake\ORM\Table;
use IdeHelper\Filesystem\Folder;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;
use IdeHelper\ValueObject\StringName;

class BehaviorTask implements TaskInterface {

	public const CLASS_TABLE = Table::class;

	/**
	 * @var array<string, int>
	 */
	protected array $addAliases = [
		'\\' . self::CLASS_TABLE . '::addBehavior()' => 0,
	];

	/**
	 * @var array<string, int>
	 */
	protected array $removeAliases = [
		'\\' . self::CLASS_TABLE . '::removeBehavior()' => 0,
	];

	/**
	 * @var array<string, int>
	 */
	protected array $hasAliases = [
		'\\' . self::CLASS_TABLE . '::hasBehavior()' => 0,
	];

	/**
	 * @var array<string>
	 */
	protected array $getAliases = [
		'\\' . self::CLASS_TABLE . '::getBehavior()',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$prefixedList = $nonPrefixedList = [];
		$behaviors = $this->collectBehaviors();
		foreach ($behaviors as $name => $className) {
			$prefixedList[$name] = StringName::create($name);
			if (str_contains($name, '.')) {
				[, $name] = pluginSplit($name);
			}
			$nonPrefixedList[$name] = StringName::create($name);
		}

		ksort($prefixedList);
		ksort($nonPrefixedList);

		$result = [];
		foreach ($this->addAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $prefixedList);
			$result[$directive->key()] = $directive;
		}
		foreach ($this->removeAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $nonPrefixedList);
			$result[$directive->key()] = $directive;
		}
		foreach ($this->hasAliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, $nonPrefixedList);
			$result[$directive->key()] = $directive;
		}
		foreach ($this->getAliases as $alias) {
			$map = [];
			foreach ($behaviors as $name => $className) {
				if (str_contains($name, '.')) {
					[, $name] = pluginSplit($name);
				}
				$map[$name] = ClassName::create($className);
			}

			ksort($map);

			$directive = new Override($alias, $map);
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
			$fullName = $matches[1];
			$name = $fullName;
			if ($plugin) {
				$name = $plugin . '.' . $fullName;
			}

			$className = App::className($name, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = "Cake\ORM\Behavior\\{$name}Behavior";
			}

			$behaviors[$name] = $className;

		}

		return $behaviors;
	}

}
