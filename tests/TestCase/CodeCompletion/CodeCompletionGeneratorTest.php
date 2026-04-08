<?php

namespace IdeHelper\Test\TestCase\CodeCompletion;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;

class CodeCompletionGeneratorTest extends TestCase {

	protected CodeCompletionGenerator $generator;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['MyNamespace/MyPlugin', 'Shim']);
		$taskCollection = new TaskCollection();
		$this->generator = new CodeCompletionGenerator($taskCollection);

		$file = TMP . 'CodeCompletionCakeORM.php';
		if (file_exists($file)) {
			unlink($file);
		}
		$file = TMP . 'CodeCompletionCakeORMQuery.php';
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
			'Cake\ORM\Query',
			'Cake\View',
		];

		$this->assertSame($expected, $result);
		$this->assertFileExists(TMP . 'CodeCompletionCakeORM.php');
		$this->assertFileExists(TMP . 'CodeCompletionCakeORMQuery.php');

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
use Cake\ORM\Query\SelectQuery;
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

		$result = file_get_contents(TMP . 'CodeCompletionCakeORMQuery.php');

		$expected = <<<'TXT'
<?php
namespace Cake\ORM\Query;

/**
 * Only for code completion - regenerate using `bin/cake code_completion generate`.
 */
use Cake\Database\ExpressionInterface;
use Cake\Datasource\ResultSetInterface;
use Closure;
use Psr\SimpleCache\CacheInterface;

if (false) {
	/**
	 * @template TSubject
	 */
	class SelectQuery {
		/**
		 * @return static<TSubject>
		 */
		public function find(string $finder, mixed ...$args) {}

		/**
		 * @return static<TSubject>
		 */
		public function where(
			ExpressionInterface|Closure|array|string|null $conditions = null,
			array $types = [],
			bool $overwrite = false,
		) {}

		/**
		 * @return static<TSubject>
		 */
		public function andWhere($conditions, array $types = []) {}

		/**
		 * @return static<TSubject>
		 */
		public function matching(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function leftJoinWith(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function innerJoinWith(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function notMatching(string $assoc, ?Closure $builder = null) {}

		/**
		 * @return static<TSubject>
		 */
		public function contain(mixed $associations, Closure|bool $override = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function clearContain() {}

		/**
		 * @return static<TSubject>
		 */
		public function cache(Closure|string|false $key, CacheInterface|string $config = 'default') {}

		/**
		 * @return static<TSubject>
		 */
		public function groupBy(ExpressionInterface|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function orderBy(ExpressionInterface|Closure|array|string $fields, bool $overwrite = false) {}

		/**
		 * @return static<TSubject>
		 */
		public function enableAutoFields(bool $value = true) {}

		/**
		 * @return static<TSubject>
		 */
		public function disableAutoFields() {}

		/**
		 * @return static<array<string,mixed>>
		 */
		public function disableHydration() {}

		/**
		 * @return ResultSetInterface<array-key, TSubject>
		 */
		public function all() {}

		/**
		 * @return TSubject|null
		 */
		public function first() {}

		/**
		 * @return TSubject
		 */
		public function firstOrFail() {}

		/**
		 * @return array<TSubject>
		 */
		public function toArray() {}
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
