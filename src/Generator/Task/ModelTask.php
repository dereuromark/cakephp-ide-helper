<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\AppPath;

class ModelTask implements TaskInterface {

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Cake\ORM\TableRegistry::get(0)',
		'\Cake\ORM\Locator\LocatorInterface::get(0)',
		'\Cake\Datasource\ModelAwareTrait::loadModel(0)',
	];

	/**
	 * Buffer
	 *
	 * @var array|null
	 */
	protected static $models;

	/**
	 * @return void
	 */
	public static function clearBuffer(): void {
		static::$models = null;
	}

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$map = [];

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$map[$model] = '\\' . $className . '::class';
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
	protected function collectModels(): array {
		if (static::$models !== null) {
			return static::$models;
		}

		$models = [];

		$folders = AppPath::get('Model/Table');
		foreach ($folders as $folder) {
			$models = $this->addModels($models, $folder);
		}

		$plugins = Plugin::loaded();
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
	 * @param array $models
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return string[]
	 */
	protected function addModels(array $models, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Table\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$model = $matches[1];
			if ($plugin) {
				$model = $plugin . '.' . $model;
			}

			try {
				$className = App::className($model, 'Model/Table', 'Table');
			} catch (\Exception $e) {
				continue;
			} catch (\Throwable $e) {
				continue;
			}
			if (!$className) {
				continue;
			}

			$models[$model] = $className;
		}

		return $models;
	}

}
