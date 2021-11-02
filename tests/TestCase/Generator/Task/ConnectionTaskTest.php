<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\ConnectionTask;
use Shim\TestSuite\TestCase;

class ConnectionTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ConnectionTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ConnectionTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Datasource\ConnectionManager::get()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'test' => "'test'",
		];
		$this->assertSame($expected, $list);
	}

}
