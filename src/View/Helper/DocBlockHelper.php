<?php
namespace IdeHelper\View\Helper;

use Bake\View\Helper\DocBlockHelper as BakeDocBlockHelper;

class DocBlockHelper extends BakeDocBlockHelper {

	/**
	 * Overwrite Bake plugin class method until https://github.com/cakephp/bake/pull/470 lands.
	 *
	 * @param array $propertySchema The property schema to use for generating the type map.
	 * @return array The property DocType map.
	 */
	public function buildEntityPropertyHintTypeMap(array $propertySchema) {
		$properties = [];
		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'column') {
				$type = $this->columnTypeToHintType($info['type']);
				if (!empty($info['null'])) {
					$type .= '|null';
				}
				$properties[$property] = $type;
			}
		}

		return $properties;
	}

}
