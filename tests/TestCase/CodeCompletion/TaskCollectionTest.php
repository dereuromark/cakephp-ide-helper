<?php

namespace IdeHelper\Test\TestCase\CodeCompletion;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\TaskCollection;

class TaskCollectionTest extends TestCase {

	protected TaskCollection $taskCollection;

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
