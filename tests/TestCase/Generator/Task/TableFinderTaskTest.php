<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\TableFinderTask;
use Tools\TestSuite\TestCase;
use Tools\TestSuite\ToolsTestTrait;

class TableFinderTaskTest extends TestCase {

	use ToolsTestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\TableFinderTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->task = new TableFinderTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		$expectedMap = [
			'all' => '\Cake\ORM\Query::class',
			'list' => '\Cake\ORM\Query::class',
			'threaded' => '\Cake\ORM\Query::class',
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

	/**
	 * @return void
	 */
	public function testAddMethod() {
		$result = [];
		$method = 'findSomethingCustom';

		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method]);
		$this->assertSame(['somethingCustom'], $result);
	}

	/**
	 * @return void
	 */
	public function testAddMethodInvalid() {
		$result = [];

		$method = 'findBySomethingCustom';
		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method]);
		$this->assertSame([], $result);

		$method = 'findSomethingCustomBySomethingElse';
		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method]);
		$this->assertSame([], $result);
	}

}
