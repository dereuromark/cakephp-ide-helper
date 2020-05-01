<?php

namespace IdeHelper\Utility;

use Cake\Core\Exception\MissingPluginException;

class PluginPath {

	/**
	 * @param string $plugin
	 * @return string
	 * @throws \Cake\Core\Exception\MissingPluginException
	 */
	public static function get($plugin) {
		try {
			return Plugin::path($plugin);
		} catch (MissingPluginException $exception) {
		}

		$pathToPlugin = Plugin::getCollection()->findPath($plugin);
		Plugin::load($plugin);

		return $pathToPlugin;
	}

}
