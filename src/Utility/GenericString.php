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
		$typeCheck = $type !== null && str_starts_with($type, '\\') ? substr($type, 1) : $type;

		// ResultSetInterface declares two template params (TKey, TValue); always emit both so PHPStan's
		// missingType.generics stays clean. Keys are int for a result set. PHPStorm handles the object generic.
		if ($typeCheck === ResultSetInterface::class) {
			if (Configure::read('IdeHelper.genericsInParam') === 'detailed' || Configure::read('IdeHelper.concreteEntitiesInParam')) {
				return sprintf($type . '<int, %s>', $value);
			}

			return $value . '[]|' . $type . '<int, ' . $value . '>';
		}

		if (Configure::read('IdeHelper.arrayAsGenerics') && ($type === null || in_array($type, ['array', 'iterable'], true))) {
			return sprintf(($type ?: 'array' ) . '<%s>', $value);
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
