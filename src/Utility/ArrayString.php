<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;

class ArrayString {

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public static function generate(string $value): string {
		if (Configure::read('IdeHelper.arrayAsGenerics')) {
			return sprintf('array<%s>', $value);
		}

		return $value . '[]';
	}

}
