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
	protected function setUp(): void {
		parent::setUp();

		$this->task = new BehaviorTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\Table::addBehavior()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expected = '\'Timestamp\'';
		$this->assertSame($expected, (string)$list['Timestamp']);

		$expected = '\'Shim.Nullable\'';
		$this->assertSame($expected, (string)$list['Shim.Nullable']);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\Table::removeBehavior()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expected = '\'Timestamp\'';
		$this->assertSame($expected, (string)$list['Timestamp']);

		$expected = '\'Nullable\'';
		$this->assertSame($expected, (string)$list['Nullable']);
	}

}
