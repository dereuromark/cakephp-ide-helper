<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\EnvTask;
use Shim\TestSuite\TestCase;

class EnvTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\EnvTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new EnvTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\env()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'HTTP_HOST' => "'HTTP_HOST'",
			'REMOTE_ADDR' => "'REMOTE_ADDR'",
		];
		foreach ($expected as $key => $value) {
			$this->assertSame($value, $list[$key]);
		}
	}

}
