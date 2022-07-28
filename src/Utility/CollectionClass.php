<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;

class CollectionClass {

	/**
	 * @param string $name FQCN
	 *
	 * @return string
	 */
	public static function name(string $name): string {
		$configured = Configure::read('IdeHelper.templateCollectionObject');
		if ($configured === null || $configured === true) {
			return $name;
		}
		if ($configured === false) {
			return 'array';
		}
		if (!is_string($configured)) {
			throw new CakeException('`templateCollectionObject` config must be `string|bool|null`.');
		}

		return $configured;
	}

}
