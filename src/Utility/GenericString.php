<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;

class GenericString {

	/**
	 * @param string $value
	 * @param string|null $type
	 *
	 * @return string
	 */
	public static function generate(string $value, ?string $type = null): string {
		if (Configure::read('IdeHelper.arrayAsGenerics') && ($type === null || in_array($type, ['array', 'iterable'], true))) {
			return sprintf(($type ?: 'array' ) . '<%s>', $value);
		}
		if (Configure::read('IdeHelper.objectAsGenerics') && $type !== null) {
			return sprintf($type . '<%s>', $value);
		}

		if ($type !== null && str_starts_with($type, '\\')) {
			$typeCheck = substr($type, 1);
		} else {
			$typeCheck = $type;
		}

		if ($typeCheck === ResultSetInterface::class) {
			if (Configure::read('IdeHelper.useConcreteEntities')) {
				return sprintf($type . '<%s>', $value);
			}

			return $value . '[]|' . $type . '<' . $value . '>';
		}
		
		$value .= '[]';
		if ($type) {
			$value .= '|' . $type;
		}

		return $value;
	}

}
