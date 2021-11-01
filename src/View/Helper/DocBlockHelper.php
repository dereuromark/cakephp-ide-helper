<?php

namespace IdeHelper\View\Helper;

use Bake\View\Helper\DocBlockHelper as BakeDocBlockHelper;
use Cake\Core\Configure;
use Cake\ORM\Association;

class DocBlockHelper extends BakeDocBlockHelper {

	/**
	 * @var array<string, bool>|null
	 */
	protected static $nullableMap;

	/**
	 * Overwrite Bake plugin class method until https://github.com/cakephp/bake/pull/470 lands.
	 *
	 * @param array<array<string, mixed>> $propertySchema The property schema to use for generating the type map.
	 * @return array<string, string> The property DocType map.
	 */
	public function buildEntityPropertyHintTypeMap(array $propertySchema): array {
		$properties = [];
		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'column') {
				$type = $this->columnTypeToHintType($info['type']);

				$properties[$property] = $this->columnTypeNullable($info, $type);
			}
		}

		return $properties;
	}

	/**
	 * @param array<string, string> $info
	 * @param string|null $type
	 * @param string|null $default
	 *
	 * @return string
	 */
	public function columnTypeNullable(array $info, ?string $type, ?string $default = null): string {
		if (!$type) {
			$type = $default ?: 'mixed';
		}

		if ($type === 'mixed' || empty($info['null'])) {
			return $type;
		}

		if (static::$nullableMap === null) {
			static::$nullableMap = (array)Configure::read('IdeHelper.nullableMap');
		}

		if (isset(static::$nullableMap[$type]) && static::$nullableMap[$type] === false) {
			return $type;
		}

		$type .= '|null';

		return $type;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Overwrite with nullable option for now until Bake is adjusted ( https://github.com/cakephp/bake/issues/579 )
	 *
	 * @param array<string, array<string, mixed>> $propertySchema The property schema to use for generating the type map.
	 * @return array<string, string> The property DocType map.
	 */
	public function buildEntityAssociationHintTypeMap(array $propertySchema): array {
		$properties = [];
		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'association') {
				$type = $this->associatedEntityTypeToHintType($info['type'], $info['association']);
				if ($info['association']->type() === Association::MANY_TO_ONE) {
					$key = $info['association']->getForeignKey();
					if (is_array($key)) {
						$key = implode('-', $key);
					}
					$properties = $this->_insertAfter(
						$properties,
						$key,
						[$property => $this->columnTypeNullable($info, $type)],
					);
				} else {
					$properties[$property] = $this->columnTypeNullable($info, $type);
				}
			}
		}

		return $properties;
	}

}
