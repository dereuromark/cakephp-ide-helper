<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\BehaviorTask;

class BehaviorTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\Task\BehaviorTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new BehaviorTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->create();

		$expected = <<<'TXT'
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

TXT;
		$this->assertTextEquals($expected, $result);
	}

}
