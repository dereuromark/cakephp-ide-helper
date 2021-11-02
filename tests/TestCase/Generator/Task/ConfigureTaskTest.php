<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\ConfigureTask;
use Shim\TestSuite\TestTrait;

class ConfigureTaskTest extends TestCase {

	use TestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\ConfigureTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ConfigureTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(8, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertInstanceOf(RegisterArgumentsSet::class, $directive);
		$this->assertSame(ConfigureTask::SET_CONFIGURE_KEYS, $directive->toArray()['set']);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Core\Configure::read()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'argumentsSet(\'configureKeys\')',
		];
		$this->assertSame($expected, $list);
	}

	/**
	 * @return void
	 */
	public function testCollectKeys(): void {
		$result = $this->invokeMethod($this->task, 'collectKeys');

		$this->assertArrayHasKey('App.paths.templates', $result);
		$this->assertArrayNotHasKey('paths', $result);
		$this->assertArrayNotHasKey('templates', $result);

		$this->assertSame('\'debug\'', (string)$result['debug']);
	}

}
