<?php

namespace IdeHelper\Test\TestCase\CodeCompletion;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;

class CodeCompletionGeneratorTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\CodeCompletionGenerator
	 */
	protected $generator;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$taskCollection = new TaskCollection();
		$this->generator = new CodeCompletionGenerator($taskCollection);

		$file = TMP . 'CodeCompletionCakeORM.php';
		if (file_exists($file)) {
			unlink($file);
		}
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->generator->generate();

		$expected = [
			'Cake\Controller',
			'Cake\ORM',
			'Cake\View',
		];

		$this->assertSame($expected, $result);
		$this->assertFileExists(TMP . 'CodeCompletionCakeORM.php');

		$result = file_get_contents(TMP . 'CodeCompletionCakeORM.php');

		$expected = <<<'TXT'
<?php
namespace Cake\ORM;

/**
 * Only for code completion - regenerate using `bin/cake code_completion generate`.
 */
abstract class BehaviorRegistry extends \Cake\Core\ObjectRegistry {

	/**
	 * MyNamespace/MyPlugin.My behavior.
	 *
	 * @var \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
	 */
	public $My;

	/**
	 * Shim.Nullable behavior.
	 *
	 * @var \Shim\Model\Behavior\NullableBehavior
	 */
	public $Nullable;

}

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
