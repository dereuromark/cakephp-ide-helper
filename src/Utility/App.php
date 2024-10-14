<?php

namespace IdeHelper\Utility;

use Cake\Core\App as CoreApp;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Throwable;

class App extends CoreApp {

	/**
	 * @param string $class
	 * @param string $type
	 * @param string $suffix
	 * @param bool $includeApp
	 *
	 * @return string|null
	 */
	public static function className(string $class, string $type = '', string $suffix = '', bool $includeApp = true): ?string {
		try {
			if (str_contains($class, '\\')) {
				return class_exists($class) ? $class : null;
			}

			[$plugin, $name] = pluginSplit($class);
			$fullname = '\\' . str_replace('/', '\\', $type . '\\' . $name) . $suffix;

			$appNamespace = $includeApp ? Configure::read('App.namespace') : null;
			$base = $plugin ?: $appNamespace;
			if ($base !== null) {
				$base = str_replace('/', '\\', rtrim($base, '\\'));

				if (static::_classExistsInBase($fullname, $base)) {
					/** @var class-string */
					return $base . $fullname;
				}
			}

			if ($plugin || !static::_classExistsInBase($fullname, 'Cake')) {
				return null;
			}

			/** @var class-string */
			return 'Cake' . $fullname;
		} catch (Throwable $e) {
			// Do nothing
		}

		return null;
	}

	/**
	 * @param string $class
	 * @param string $type
	 * @param string $suffix
	 *
	 * @throws \Cake\Http\Exception\NotFoundException
	 *
	 * @return string
	 */
	public static function classNameOrFail(string $class, string $type = '', string $suffix = ''): string {
		$className = parent::className($class, $type, $suffix);
		if (!$className) {
			throw new NotFoundException('Class not found.');
		}

		return $className;
	}

}
