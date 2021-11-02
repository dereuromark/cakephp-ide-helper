<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ConsoleTask;

class ConsoleTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ConsoleTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ConsoleTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExitPoint $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Console\ConsoleIo::abort()', $directive->toArray()['method']);
	}

}
