<?php

namespace IdeHelper\Generator\Task;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;
use Throwable;

class EntityTask extends ModelTask {

	/**
	 * @var string
	 */
	public const SET_ENTITY_FIELDS = 'entityFields';

	/**
	 * @var array<int> array<string, int>
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
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
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
	 * @return array<array<string>>
	 */
	protected function getEntityFields(): array {
		$modelFields = [];

		$models = $this->collectModels();
		foreach ($models as $model => $className) {
			$fields = [];
			$tableObject = null;
			try {
				/** @var \Cake\ORM\Table $tableObject */
				$tableObject = new $className();
				$fields = $tableObject->getSchema()->columns();

			} catch (Throwable $exception) {
				// Do nothing
			}

			if ($tableObject) {
				try {
					$fieldsFromRelations = $this->addFromRelations($tableObject);
					$fields = array_merge($fields, $fieldsFromRelations);
					$fields = array_unique($fields);
				} catch (Throwable $exception) {
					// Do nothing
				}
			}

			$entityClass = $tableObject ? $tableObject->getEntityClass() : null;
			try {
				/** @var \Cake\Datasource\EntityInterface $entityObject */
				$entityObject = new $entityClass();
				$visibleFields = $entityObject->getVisible();
				$virtualFields = $entityObject->getVirtual();
				$fields = array_merge($fields, $virtualFields, $visibleFields);
				$fields = array_unique($fields);
			} catch (Throwable $exception) {
				// Do nothing
			}

			if (!$fields) {
				continue;
			}

			$list = [];
			foreach ($fields as $field) {
				$list[$field] = StringName::create($field);
			}

			ksort($list);

			$modelFields[$entityClass] = $list;
		}

		return $modelFields;
	}

	/**
	 * @param \Cake\ORM\Table $table
	 *
	 * @return array<string>
	 */
	protected function addFromRelations(Table $table): array {
		$fields = [];

		/** @var \Cake\ORM\AssociationCollection<\Cake\ORM\Association> $associations */
		$associations = $table->associations();

		foreach ($associations as $association) {
			$fields[] = $association->getProperty();
		}

		return $fields;
	}

}
