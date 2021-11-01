<?php

namespace IdeHelper\Utility;

use Cake\Core\App;
use Cake\Core\Exception\MissingPluginException;

class AppPath {

	/**
	 * @param string $type
	 * @param string|null $plugin
	 * @throws \Cake\Core\Exception\MissingPluginException
	 * @return array<string>
	 */
	public static function get(string $type, ?string $plugin = null): array {
		try {
			return App::classPath($type, $plugin);
		} catch (MissingPluginException $exception) {
		}

		return App::classPath($type, $plugin);
	}

}
