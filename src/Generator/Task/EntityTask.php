<?php

namespace IdeHelper\Generator\Task;

use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

class EntityTask extends ModelTask {

	public const SET_ENTITY_FIELDS = 'entityFields';

	/**
	 * @var int[] array<string, int>
	 */
	public static $methods = [
		'has' => 0,
		'get' => 0,
		'hasValue' => 0,
		'isEmpty' => 0,
		'isDirty' => 0,
		'getOriginal' => 0,
		'setDirty' => 0,
		'setError' => 0,
		'getError' => 0,
		'getInvalidField' => 0,
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$fields = $this->getEntityFields();

		$result = [];
		foreach ($fields as $entityClass => $entityFields) {
			$registerArgumentsSet = new RegisterArgumentsSet(static::SET_ENTITY_FIELDS . ':' . $entityClass, $entityFields);
			$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

			foreach (static::$methods as $method => $position) {
				$entityMethod = '\\' . $entityClass . '::' . $method . '()';
				$directive = new ExpectedArguments($entityMethod, $position, [$registerArgumentsSet]);
				$result[$directive->key()] = $directive;
			}
		}

		return $result;
	}

	/**
	 * @return string[][]
	 */
	protected function getEntityFields(): array {
		$modelFields = [];

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$fields = [];
			try {
				/** @var \Cake\ORM\Table $tableObject */
				$tableObject = new $className();
				$fields = $tableObject->getSchema()->columns();
			} catch (\Exception $exception) {
				// Do nothing
			}

			if (!$fields) {
				continue;
			}

			$entityClass = $tableObject->getEntityClass();

			$list = [];
			foreach ($fields as $field) {
				$list[$field] = StringName::create($field);
			}

			ksort($list);

			$modelFields[$entityClass] = $list;
		}

		return $modelFields;
	}

}
