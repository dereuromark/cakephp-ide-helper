<?php

namespace IdeHelper\Utility;

use Cake\Core\App as CoreApp;
use Cake\Http\Exception\NotFoundException;
use Throwable;

class App extends CoreApp {

	/**
	 * @param string $class
	 * @param string $type
	 * @param string $suffix
	 *
	 * @return string|null
	 */
	public static function className(string $class, string $type = '', string $suffix = ''): ?string {
		try {
			return parent::className($class, $type, $suffix);
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
