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
	public static function get($type, $plugin = null) {
		try {
			return App::path($type, $plugin);
		} catch (MissingPluginException $exception) {
		}

		$pathToPlugin = Plugin::getCollection()->findPath($plugin);
		Plugin::load($plugin);

		$pathToClass = $pathToPlugin . 'src' . DS . $type . DS;

		return [$pathToClass];
	}

}
