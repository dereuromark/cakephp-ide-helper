<?php
namespace IdeHelper\Generator\Task;

use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Exception;
use ReflectionClass;

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

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$customFinders = $this->getFinderMethods($className);
			$customFinders = array_diff($customFinders, $baseFinders);

			try {
				$modelObject = TableRegistry::get($model);
				$behaviors = $modelObject->behaviors();
				$finderMap = $this->invokeProperty($behaviors, '_finderMap');
				$customFinders = array_merge($customFinders, array_keys($finderMap));
				$customFinders = array_unique($customFinders);

			} catch (Exception $exception) {
			}

			if (!$customFinders) {
				continue;
			}

			$finders[$model] = $customFinders;
		}

		return $finders;
	}

	/**
	 * Gets protected/private property of a class.
	 *
	 * So
	 *   $this->invokeProperty($object, '_foo');
	 * is equal to
	 *   $object->_foo
	 * (assuming the property was directly publicly accessible)
	 *
	 * @param object &$object Instantiated object that we want the property off.
	 * @param string $name Property name to fetch.
	 *
	 * @return mixed Property value.
	 */
	protected function invokeProperty(&$object, $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
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
			// We must exclude all find...By... patterns as possible false positives for now (refs https://github.com/cakephp/cakephp/issues/11240)
			if ($method === 'findOrCreate' || preg_match('/^find.*By[A-Z][a-zA-Z]+/', $method)) {
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
