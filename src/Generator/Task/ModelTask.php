<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;

class ModelTask implements TaskInterface {

	/**
	 * @var array
	 */
	protected $aliases = [
		'\Cake\ORM\TableRegistry::get(0)',
		'\Cake\ORM\Locator\LocatorInterface::get(0)',
		'\Cake\Datasource\ModelAwareTrait::loadModel(0)',
		'\ModelAwareTrait::loadModel(0)',
	];

	/**
	 * @return array
	 */
	public function collect() {
		$map = [];

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$map[$model] = '\\' . $className . '::class';
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
	protected function collectModels() {
		$models = [];

		$folders = App::path('Model/Table');
		foreach ($folders as $folder) {
			$models = $this->addModels($models, $folder);
		}

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$folders = App::path('Model/Table', $plugin);
			foreach ($folders as $folder) {
				$models = $this->addModels($models, $folder, $plugin);
			}
		}

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

			$className = App::className($model, 'Model/Table', 'Table');
			if (!$className) {
				continue;
			}

			$models[$model] = $className;
		}

		return $models;
	}

}
