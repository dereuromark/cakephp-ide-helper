<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;
use Cake\Core\Plugin as CorePlugin;

class Plugin extends CorePlugin {

	/**
	 * @return array<string>
	 */
	public static function all(): array {
		$plugins = static::loaded();
		$plugins = array_combine($plugins, $plugins);

		$pluginMap = (array)Configure::read('IdeHelper.plugins');
		foreach ($pluginMap as $plugin) {
			if (strpos($plugin, '-') === 0) {
				$plugin = substr($plugin, 1);
				unset($plugins[$plugin]);

				continue;
			}

			$plugins[$plugin] = $plugin;
		}

		return $plugins;
	}

}
