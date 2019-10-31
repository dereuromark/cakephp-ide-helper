<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\BaseApplication;
use IdeHelper\Generator\Directive\Override;

class PluginTask implements TaskInterface {

	/**
	 * We need to use this until PHPStorm fixed the issue around concrete classes here
	 */
	const INTERFACE_APPLICATION = PluginApplicationInterface::class;

	const CLASS_APPLICATION = BaseApplication::class;

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\\' . self::INTERFACE_APPLICATION . '::addPlugin(0)',
	];

	/**
	 * @return array
	 */
	public function collect(): array {
		$map = [];

		$plugins = $this->collectPlugins();
		foreach ($plugins as $name) {
			$map[$name] = '\\' . static::CLASS_APPLICATION . '::class';
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
	 * Read from PluginCollection loaded config.
	 *
	 * @return string[]
	 */
	protected function collectPlugins(): array {
		$plugins = (array)Configure::read('plugins');

		$names = array_keys($plugins);

		sort($names);

		return $names;
	}

}
