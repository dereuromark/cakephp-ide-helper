<?php

namespace IdeHelper\Test\TestCase\Generator;

use IdeHelper\Generator\TaskCollection;
use Cake\TestSuite\TestCase;

class TaskCollectionTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @return void
	 */
	public function setUp(): void {
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
