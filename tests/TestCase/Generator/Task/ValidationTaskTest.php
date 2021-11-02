<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\ValidationTask;
use Shim\TestSuite\TestCase;

class ValidationTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ValidationTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ValidationTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(15, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertInstanceOf(RegisterArgumentsSet::class, $directive);
		$this->assertSame(ValidationTask::SET_VALIDATION_WHEN, $directive->toArray()['set']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			"'create'",
			"'update'",
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Validation\Validator::requirePresence()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'argumentsSet(\'validationWhen\')',
		];
		$this->assertSame($expected, $list);
	}

}
