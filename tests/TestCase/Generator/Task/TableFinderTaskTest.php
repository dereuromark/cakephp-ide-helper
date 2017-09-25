<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\TableFinderTask;
use Tools\TestSuite\TestCase;

class TableFinderTaskTest extends TestCase {

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

}
