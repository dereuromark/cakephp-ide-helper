<?php

namespace IdeHelper\Generator\Task;

use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;
use ReflectionClass;

class ModelTask implements TaskInterface {

	/**
	 * @var array<string>
	 */
	protected $aliases = [
		'\Cake\ORM\TableRegistry::get(0)',
		'\Cake\ORM\Locator\LocatorInterface::get(0)',
		'\Cake\ORM\Locator\LocatorAwareTrait::fetchTable(0)',
		'\Cake\Datasource\ModelAwareTrait::loadModel(0)',
	];

	/**
	 * Buffer
	 *
	 * @var array<string, string>|null
	 */
	protected static $models;

	/**
	 * @return void
	 */
	public static function clearBuffer(): void {
		static::$models = null;
	}

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$map = [];

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$map[$model] = ClassName::create($className);
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
	 * @return array<string, string>
	 */
	protected function collectModels(): array {
		if (static::$models !== null) {
			return static::$models;
		}

		$models = [];

		$folders = AppPath::get('Model/Table');
		foreach ($folders as $folder) {
			$models = $this->addModels($models, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Model/Table', $plugin);
			foreach ($folders as $folder) {
				$models = $this->addModels($models, $folder, $plugin);
			}
		}

		static::$models = $models;

		return $models;
	}

	/**
	 * @param array<string, string> $models
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string, string>
	 */
	protected function addModels(array $models, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Table\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			/** @var string $model */
			$model = $matches[1];
			if ($plugin) {
				$model = $plugin . '.' . $model;
			}

			/** @phpstan-var class-string<object>|null $className */
			$className = App::className($model, 'Model/Table', 'Table');
			if (!$className) {
				continue;
			}

			$reflectionClass = new ReflectionClass($className);
			if (!$reflectionClass->isInstantiable() || !$reflectionClass->isSubclassOf(Table::class)) {
				continue;
			}

			$models[$model] = $className;
		}

		return $models;
	}

}
