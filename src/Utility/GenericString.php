<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;

class GenericString {

	/**
	 * @param string $value
	 * @param string|null $type
	 *
	 * @return string
	 */
	public static function generate(string $value, ?string $type = null): string {
		if (Configure::read('IdeHelper.arrayAsGenerics') && $type === null) {
			return sprintf('array<%s>', $value);
		}
		if (Configure::read('IdeHelper.objectAsGenerics') && $type !== null) {
			return sprintf($type . '<%s>', $value);
		}

		$value .= '[]';
		if ($type) {
			$value .= '|' . $type;
		}

		return $value;
	}

}
