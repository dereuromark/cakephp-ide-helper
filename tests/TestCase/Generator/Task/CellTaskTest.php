<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\CellTask;

class CellTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\CellTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new CellTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\CellTrait::cell()', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\TestApp\View\Cell\TestCell::class';
		$this->assertSame($expected, (string)$map['Test']);
	}

}
