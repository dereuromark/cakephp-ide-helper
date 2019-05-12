<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\BehaviorTask;

class BehaviorTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\BehaviorTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new BehaviorTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		$expected = '\Cake\ORM\Table::class';
		$this->assertSame($expected, $result['\Cake\ORM\Table::addBehavior(0)']['Timestamp']);

		$expected = '\Cake\ORM\Table::class';
		$this->assertSame($expected, $result['\Cake\ORM\Table::addBehavior(0)']['Shim.Nullable']);
	}

}
