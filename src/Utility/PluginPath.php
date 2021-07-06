<?php

namespace IdeHelper\Utility;

use Cake\Core\Exception\MissingPluginException;

class PluginPath {

	/**
	 * @param string $plugin
	 * @throws \Cake\Core\Exception\MissingPluginException
	 * @return string
	 */
	public static function get(string $plugin): string {
		try {
			return Plugin::path($plugin);
		} catch (MissingPluginException $exception) {
		}

		return Plugin::path($plugin);
	}

	/**
	 * @param string $plugin
	 *
	 * @return string
	 */
	public static function classPath(string $plugin): string {
		try {
			return Plugin::classPath($plugin);
		} catch (MissingPluginException $exception) {
		}

		return Plugin::classPath($plugin);
	}

}
