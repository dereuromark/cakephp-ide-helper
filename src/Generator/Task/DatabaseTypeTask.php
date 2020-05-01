<?php

namespace IdeHelper\Generator\Task;

use Cake\Database\Type;
use Exception;
use IdeHelper\Generator\Directive\Override;
use Throwable;

class DatabaseTypeTask implements TaskInterface {

	const CLASS_TYPE = Type::class;

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect() {
		$result = [];

		$types = $this->getTypes();
		$map = [];
		foreach ($types as $type => $className) {
			$map[$type] = '\\' . $className . '::class';
		}

		ksort($map);

		$method = '\\' . static::CLASS_TYPE . '::build(0)';
		$directive = new Override($method, $map);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function getTypes() {
		$types = [];

		try {
			$allTypes = Type::buildAll();
		} catch (Exception $exception) {
			return $types;
		} catch (Throwable $exception) {
			return $types;
		}

		foreach ($allTypes as $key => $type) {
			$types[$key] = get_class($type);
		}

		ksort($types);

		return $types;
	}

}
