<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ConsoleHelperTask;

class ConsoleHelperTaskTest extends TestCase {

	protected ConsoleHelperTask $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ConsoleHelperTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Console\ConsoleIo::helper(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$namespace = class_exists('Cake\Console\Helper\ProgressHelper') ? 'Console' : 'Command';
		$expected = sprintf('\Cake\%s\Helper\ProgressHelper::class', $namespace);
		$this->assertSame($expected, (string)$map['Progress']);

		$expected = sprintf('\Cake\%s\Helper\TableHelper::class', $namespace);
		$this->assertSame($expected, (string)$map['Table']);
	}

}
