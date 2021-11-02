<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\HelperTask;

class HelperTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\HelperTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new HelperTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\View::loadHelper(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\Cake\View\Helper\FormHelper::class';
		$this->assertSame($expected, (string)$map['Form']);

		$expected = '\Shim\View\Helper\ConfigureHelper::class';
		$this->assertSame($expected, (string)$map['Shim.Configure']);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\ViewBuilder::addHelper()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expected = "'Form'";
		$this->assertSame($expected, (string)$list['Form']);

		$expected = "'Shim.Configure'";
		$this->assertSame($expected, (string)$list['Shim.Configure']);
	}

}
