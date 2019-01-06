<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use Cake\Http\BaseApplication;

class PluginTask implements TaskInterface {

	const CLASS_APPLICATION = BaseApplication::class;

	/**
	 * @var array
	 */
	protected $aliases = [
		'\\' . self::CLASS_APPLICATION . '::addPlugin(0)',
	];

	/**
	 * @return array
	 */
	public function collect() {
		$map = [];

		$plugins = $this->collectPlugins();
		foreach ($plugins as $name) {
			$map[$name] = '\\' . self::CLASS_APPLICATION . '::class';
		}

		$result = [];
		foreach ($this->aliases as $alias) {
			$result[$alias] = $map;
		}

		return $result;
	}

	/**
	 * Read from PluginCollection loaded config.
	 *
	 * @return string[]
	 */
	protected function collectPlugins() {
		$plugins = Configure::read('plugins');

		return array_keys($plugins);
	}

}
