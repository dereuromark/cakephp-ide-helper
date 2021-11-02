<?php

namespace IdeHelper\Test\TestCase\Generator;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\TaskCollection;

class TaskCollectionTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @return void
	 */
	protected function setUp(): void {
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
