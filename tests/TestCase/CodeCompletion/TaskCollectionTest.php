<?php

namespace IdeHelper\Test\TestCase\CodeCompletion;

use IdeHelper\CodeCompletion\TaskCollection;
use Tools\TestSuite\TestCase;

class TaskCollectionTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\TaskCollection
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
	public function testCollect() {
		$result = $this->taskCollection->tasks();

		$this->assertNotEmpty($result);
	}

}
