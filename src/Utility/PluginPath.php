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

}
