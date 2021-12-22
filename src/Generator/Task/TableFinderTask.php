<?php

namespace IdeHelper\Generator\Task;

use Cake\Datasource\QueryInterface;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\ValueObject\ClassName;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class TableFinderTask extends ModelTask {

	/**
	 * @var string
	 */
	public const INTERFACE_QUERY = QueryInterface::class;

	/**
	 * @var string
	 */
	public const CLASS_TABLE = Table::class;

	/**
	 * @var string
	 */
	public const CLASS_ASSOCIATION = Association::class;

	/**
	 * @var string
	 */
	public const CLASS_QUERY = Query::class;

	/**
	 * @var array<string>
	 */
	protected $cache = [];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$finders = $this->collectFinders();
		foreach ($finders as $className => $methods) {
			$map = [];
			foreach ($methods as $method) {
				$map[$method] = ClassName::create(static::CLASS_QUERY);
			}

			ksort($map);

			$method = '\\' . $className . '::find(0)';
			$directive = new Override($method, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string, array<string>>
	 */
	protected function collectFinders(): array {
		$baseFinders = $this->getFinderMethods(static::CLASS_TABLE);
		$customFinders = $this->getCustomFinders();

		$allFinders = array_merge($baseFinders, $customFinders);
		$allFinders = array_unique($allFinders);

		sort($allFinders);

		$finders = [];
		$finders[static::CLASS_TABLE] = $allFinders;
		$finders[static::CLASS_ASSOCIATION] = $allFinders;
		$finders[static::INTERFACE_QUERY] = $allFinders;

		return $finders;
	}

	/**
	 * @return array<string>
	 */
	protected function getCustomFinders(): array {
		$models = $this->collectModels();

		$allFinders = [];
		foreach ($models as $model => $className) {
			$customFinders = $this->getFinderMethods($className);

			/** @phpstan-var class-string<object>|null $tableClass */
			$tableClass = App::className($model, 'Model/Table', 'Table');
			if (!$tableClass) {
				continue;
			}

			$tableReflection = new ReflectionClass($tableClass);
			if (!$tableReflection->isInstantiable()) {
				$allFinders = array_merge($allFinders, $customFinders);

				continue;
			}

			try {
				$modelObject = TableRegistry::getTableLocator()->get($model);
				$behaviors = $modelObject->behaviors();

				/** @var array<\Cake\ORM\Behavior> $iterator */
				$iterator = $behaviors->getIterator();
				foreach ($iterator as $behavior) {
					$behaviorClass = get_class($behavior);
					if (in_array($behaviorClass, $this->cache, true)) {
						continue;
					}

					$this->cache[] = $behaviorClass;

					if ($behavior->implementedFinders()) {
						$customFinders = array_merge($customFinders, array_keys($behavior->implementedFinders()));
					}
				}
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
	protected function invokeProperty(&$object, string $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
	}

	/**
	 * @param string $className
	 *
	 * @return array<string>
	 */
	protected function getFinderMethods($className) {
		$result = [];

		$methods = get_class_methods($className);
		foreach ($methods as $method) {
			$result = $this->addMethod($result, $method, $className);
		}

		ksort($result);

		return $result;
	}

	/**
	 * @param array<string> $result
	 * @param string $method
	 * @param string $className
	 *
	 * @return array<string>
	 */
	protected function addMethod(array $result, $method, $className) {
		// We must exclude all find...By... patterns as possible false positives for now (refs https://github.com/cakephp/cakephp/issues/11240)
		if ($method === 'findOrCreate' || preg_match('/^find.*By[A-Z][a-zA-Z]+/', $method)) {
			return $result;
		}
		if (!preg_match('/^find([A-Z][a-zA-Z]+)/', $method, $matches)) {
			return $result;
		}

		try {
			$methodReflection = new ReflectionMethod($className, $method);
		} catch (ReflectionException $e) {
			return $result;
		}

		$parameters = $methodReflection->getParameters();
		if (count($parameters) < 1) {
			return $result;
		}

		$name = lcfirst($matches[1]);

		$parameter = $parameters[0];

		/** @var \ReflectionNamedType|\ReflectionUnionType|null $parameterType */
		$parameterType = $parameter->getType();
		if (!$parameterType || !method_exists($parameterType, 'getName') || $parameterType->getName() !== Query::class) {
			return $result;
		}

		$result[] = $name;

		return $result;
	}

}
