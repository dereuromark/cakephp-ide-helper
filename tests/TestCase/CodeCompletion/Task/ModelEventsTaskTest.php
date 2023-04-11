<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\ModelEventsTask;

class ModelEventsTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\Task\ModelEventsTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ModelEventsTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->create();

		$expected = <<<'TXT'

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

if (false) {
	abstract class Table {
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
	}

	abstract class Behavior {
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
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
