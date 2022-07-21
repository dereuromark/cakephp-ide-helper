<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;

class ArrayString {

	/**
	 * @param string $value
	 * @param string|null $type
	 *
	 * @return string
	 */
	public static function generate(string $value, ?string $type = null): string {
		if (Configure::read('IdeHelper.arrayAsGenerics')) {
			return sprintf( ($type ?: 'array') . '<%s>', $value);
		}

		return $value . '[]';
	}

}
