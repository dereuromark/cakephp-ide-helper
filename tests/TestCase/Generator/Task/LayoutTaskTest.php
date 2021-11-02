<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\LayoutTask;
use Shim\TestSuite\TestTrait;

class LayoutTaskTest extends TestCase {

	use TestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\LayoutTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new LayoutTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\ViewBuilder::setLayout()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expectedMap = [
			'ajax' => '\'ajax\'',
		];
		$this->assertSame($expectedMap, $list);
	}

}
