<?php

namespace IdeHelper\Test\TestCase\Generator;

use IdeHelper\Generator\TaskCollection;
use Tools\TestSuite\TestCase;

class TaskCollectionTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->taskCollection = new TaskCollection();
	}

	/**
	 * @return void
	 */
	public function testTasks() {
		$result = $this->taskCollection->tasks();

		$this->assertNotEmpty($result);
	}

}
