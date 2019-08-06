<?php

namespace IdeHelper\Utility;

use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;

class PluginPath {

	/**
	 * @param string $plugin
	 * @return string
	 * @throws \Cake\Core\Exception\MissingPluginException
	 */
	public static function get(string $plugin): string {
		try {
			return Plugin::path($plugin);
		} catch (MissingPluginException $exception) {
		}

		return Plugin::path($plugin);
	}

}
