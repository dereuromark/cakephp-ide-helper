<?php

namespace IdeHelper\Generator\Task;

use Cake\Filesystem\Folder;
use Cake\View\Cell;
use Cake\View\CellTrait;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class CellTask implements TaskInterface {

	public const CLASS_CELL = CellTrait::class;

	/**
	 * @var string
	 */
	protected static $alias = '\\' . self::CLASS_CELL . '::cell()';

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$map = [];

		$cells = $this->collectCells();
		foreach ($cells as $name => $className) {
			$map[$name] = ClassName::create($className);
		}

		ksort($map);

		$result = [];
		if ($map) {
			$directive = new Override(static::$alias, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectCells(): array {
		$cells = [];

		$folders = AppPath::get('View/Cell');
		foreach ($folders as $folder) {
			$cells = $this->addCells($cells, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('View/Cell', $plugin);
			foreach ($folders as $folder) {
				$cells = $this->addCells($cells, $folder, $plugin);
			}
		}

		return $cells;
	}

	/**
	 * @param array<string> $components
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addCells(array $components, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Cell\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'View/Cell', 'Cell');
			if (!$className) {
				continue;
			}

			$methods = get_class_methods($className);
			$defaultCellMethods = get_class_methods(Cell::class);

			foreach ($methods as $method) {
				if (!in_array($method, $defaultCellMethods, true)) {
					$components[$name . '::' . $method] = $className;
				}
			}

			unset($components[$name . '::display']);
			$components[$name] = $className;
		}

		return $components;
	}

}
