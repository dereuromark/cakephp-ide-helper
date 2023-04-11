<?php

namespace IdeHelper\CodeCompletion\Task;

class ModelEventsTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\ORM';

	/**
	 * @return string
	 */
	public function type(): string {
		return static::TYPE_NAMESPACE;
	}

	/**
	 * @return string
	 */
	public function create(): string {
		$events = <<<'TXT'
		public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void {}
		public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $data, ArrayObject $options): void {}
		public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary): void {}
		public function buildValidator(EventInterface $event, Validator $validator, $name): void {}
		public function buildRules(RulesChecker $rules): RulesChecker { return $rules; }
		public function beforeRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, $operation): void {}
		public function afterRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, $result, $operation): void {}
		public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterSaveCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterDeleteCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
TXT;

		return <<<CODE

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

if (false) {
	abstract class Table {
$events
	}

	abstract class Behavior {
$events
	}
}

CODE;
	}

}
