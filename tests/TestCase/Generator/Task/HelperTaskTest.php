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
	public function setUp(): void {
		parent::setUp();

		$this->task = new HelperTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\View::loadHelper(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\Cake\View\Helper\FormHelper::class';
		$this->assertSame($expected, $map['Form']);

		$expected = '\Shim\View\Helper\ConfigureHelper::class';
		$this->assertSame($expected, $map['Shim.Configure']);
	}

}
