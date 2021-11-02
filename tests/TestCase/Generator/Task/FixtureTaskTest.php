<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\FixtureTask;

class FixtureTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\FixtureTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new FixtureTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\\' . TestCase::class . '::addFixture()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'app.Houses' => '\'app.Houses\'',
			'core.Posts' => '\'core.Posts\'',
			'plugin.IdeHelper.Windows' => '\'plugin.IdeHelper.Windows\'',
			'plugin.MyNamespace/MyPlugin.Sub/My' => '\'plugin.MyNamespace/MyPlugin.Sub/My\'',
		];
		foreach ($expected as $key => $value) {
			$this->assertSame($value, $list[$key]);
		}
	}

}
