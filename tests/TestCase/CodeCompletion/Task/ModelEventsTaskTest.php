<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\ModelEventsTask;

class ModelEventsTaskTest extends TestCase {

	protected ModelEventsTask $task;

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
use Cake\Database\Query\SelectQuery;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

if (false) {
	class Table {
		public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void {}
		public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): void {}
		public function buildValidator(EventInterface $event, Validator $validator, string $name): void {}
		public function buildRules(RulesChecker $rules): RulesChecker { return $rules; }
		public function beforeRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, string $operation): void {}
		public function afterRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, bool $result, string $operation): void {}
		public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterSaveCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function afterDeleteCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
	}

	class Behavior {
		public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void {}
		public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {}
		public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): void {}
		public function buildValidator(EventInterface $event, Validator $validator, string $name): void {}
		public function buildRules(RulesChecker $rules): RulesChecker { return $rules; }
		public function beforeRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, string $operation): void {}
		public function afterRules(EventInterface $event, EntityInterface $entity, ArrayObject $options, bool $result, string $operation): void {}
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
