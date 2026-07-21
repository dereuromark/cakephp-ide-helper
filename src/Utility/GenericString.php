<?php

namespace IdeHelper\Utility;

use Cake\Core\Configure;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Datasource\ResultSetInterface;

class GenericString {

	/**
	 * Collection types that declare two template params (TKey, TValue).
	 *
	 * @var array<string>
	 */
	protected const KEY_VALUE_COLLECTIONS = [
		ResultSetInterface::class,
		PaginatedInterface::class,
	];

	/**
	 * @param string $value
	 * @param string|null $type
	 *
	 * @return string
	 */
	public static function generate(string $value, ?string $type = null): string {
		$typeCheck = $type !== null && str_starts_with($type, '\\') ? substr($type, 1) : $type;

		// ResultSetInterface and PaginatedInterface declare two template params (TKey, TValue); always emit
		// both so PHPStan's missingType.generics stays clean. Keys are int here. PHPStorm handles the object
		// generic.
		if (in_array($typeCheck, static::KEY_VALUE_COLLECTIONS, true)) {
			// Emit the generic form whenever generics are enabled for objects (`objectAsGenerics`,
			// mirroring the object handling below) or for params (`genericsInParam` true|'detailed'),
			// or when concrete entities are requested. Only the all-disabled legacy case keeps the
			// `Foo[]|...` union fallback.
			if (
				Configure::read('IdeHelper.objectAsGenerics')
				|| Configure::read('IdeHelper.genericsInParam')
				|| Configure::read('IdeHelper.concreteEntitiesInParam')
			) {
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
