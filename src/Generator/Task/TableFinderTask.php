<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use Cake\Datasource\QueryInterface;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Exception;
use ReflectionClass;
use Throwable;

class TableFinderTask extends ModelTask {

	const INTERFACE_QUERY = QueryInterface::class;
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
		$baseFinders = $this->getFinderMethods(static::CLASS_TABLE);
		$customFinders = $this->getCustomFinders();

		$allFinders = array_merge($baseFinders, $customFinders);
		$allFinders = array_unique($allFinders);

		$finders = [];
		$finders[static::CLASS_TABLE] = $allFinders;
		$finders[static::CLASS_ASSOCITATION] = $allFinders;
		$finders[static::INTERFACE_QUERY] = $allFinders;

		return $finders;
	}

	/**
	 * @return array
	 */
	protected function getCustomFinders() {
		// Currently this only works with the base Table, not specific Tables, thus the option here
		if (!Configure::read('IdeHelper.preemptive')) {
			return [];
		}

		$models = $this->collectModels();

		$allFinders = [];
		foreach ($models as $model => $className) {
			$customFinders = $this->getFinderMethods($className);

			try {
				$modelObject = TableRegistry::get($model);
				$behaviors = $modelObject->behaviors();
				$finderMap = $this->invokeProperty($behaviors, '_finderMap');
				$customFinders = array_merge($customFinders, array_keys($finderMap));

			} catch (Exception $exception) {
			} catch (Throwable $exception) {
			}

			$allFinders = array_merge($allFinders, $customFinders);
		}

		return array_unique($allFinders);
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
	 * @param object $object Instantiated object that we want the property off.
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
			$result = $this->addMethod($result, $method);
		}

		return $result;
	}

	/**
	 * @param array $result
	 * @param string $method
	 *
	 * @return array
	 */
	protected function addMethod(array $result, $method) {
		// We must exclude all find...By... patterns as possible false positives for now (refs https://github.com/cakephp/cakephp/issues/11240)
		if ($method === 'findOrCreate' || preg_match('/^find.*By[A-Z][a-zA-Z]+/', $method)) {
			return $result;
		}
		if (!preg_match('/^find([A-Z][a-zA-Z]+)/', $method, $matches)) {
			return $result;
		}

		$result[] = lcfirst($matches[1]);

		sort($result);

		return $result;
	}

}
