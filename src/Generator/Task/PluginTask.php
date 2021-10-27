<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\BaseApplication;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\ValueObject\ClassName;

class PluginTask implements TaskInterface {

	/**
	 * We need to use this until PhpStorm fixed the issue around concrete classes here
	 */
	public const INTERFACE_APPLICATION = PluginApplicationInterface::class;

	public const CLASS_APPLICATION = BaseApplication::class;

	/**
	 * @var array<string>
	 */
	protected $aliases = [
		'\\' . self::INTERFACE_APPLICATION . '::addPlugin(0)',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$map = [];

		$plugins = $this->collectPlugins();
		foreach ($plugins as $name) {
			$map[$name] = ClassName::create(static::CLASS_APPLICATION);
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
	 * @return array<string>
	 */
	protected function collectPlugins(): array {
		$plugins = (array)Configure::read('plugins');

		$names = array_keys($plugins);

		sort($names);

		return $names;
	}

}
