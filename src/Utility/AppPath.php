<?php

namespace IdeHelper\Utility;

use Cake\Core\App;
use Cake\Core\Exception\MissingPluginException;

class AppPath {

	/**
	 * @param string $type
	 * @param string|null $plugin
	 * @return array
	 * @throws \Cake\Core\Exception\MissingPluginException
	 */
	public static function get(string $type, ?string $plugin = null): array {
		try {
			return App::path($type, $plugin);
		} catch (MissingPluginException $exception) {
		}

		return App::path($type, $plugin);
	}

}
