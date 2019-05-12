<?php
namespace IdeHelper\Generator\Task;

use Cake\Database\Type;
use Exception;

class DatabaseTypeTask implements TaskInterface {

	const CLASS_TYPE = Type::class;

	/**
	 * @return array
	 */
	public function collect(): array {
		$result = [];

		$types = $this->getTypes();
		$map = [];
		foreach ($types as $type => $className) {
			$map[$type] = '\\' . $className . '::class';
		}

		$result['\\' . static::CLASS_TYPE . '::build(0)'] = $map;

		return $result;
	}

	/**
	 * @return array
	 */
	protected function getTypes() {
		$types = [];

		try {
			$allTypes = Type::buildAll();
		} catch (Exception $exception) {
			return $types;
		}

		foreach ($allTypes as $key => $type) {
			$types[$key] = get_class($type);
		}

		ksort($types);

		return $types;
	}

}
