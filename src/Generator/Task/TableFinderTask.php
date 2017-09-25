<?php
namespace IdeHelper\Generator\Task;

use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;

class TableFinderTask extends ModelTask {

	const CLASS_TABLE = Table::class;
	const CLASS_ASSOCITATION = Association::class;
	const CLASS_QUERY = Query::class;

	/**
	 * @return array
	 */
	public function collect() {
		$result = [];

		$finders = $this->collectFinders();
		foreach ($finders as $className => $methods) {
			$map = [];
			foreach ($methods as $method) {
				$map[$method] = '\\' . static::CLASS_QUERY . '::class';
			}

			$result['\\' . $className . '::find(0)'] = $map;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	protected function collectFinders() {
		$finders = [];

		$baseFinders = $this->getFinderMethods(static::CLASS_TABLE);
		$finders[static::CLASS_TABLE] = $baseFinders;
		$finders[static::CLASS_ASSOCITATION] = $baseFinders;

		//$models = $this->collectModels();
		//TODO: Specific tables and chaining (associations)

		return $finders;
	}

	/**
	 * @param string $className
	 *
	 * @return array
	 */
	protected function getFinderMethods($className) {
		$result = [];

		$methods = get_class_methods($className);
		foreach ($methods as $method) {
			if ($method === 'findOrCreate') {
				continue;
			}
			if (!preg_match('/^find([A-Z][a-zA-Z]+)/', $method, $matches)) {
				continue;
			}

			$result[] = lcfirst($matches[1]);
		}

		return $result;
	}

}
