<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\RoutePathTask;

class RoutePathTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\RoutePathTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new RoutePathTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		$this->assertSame('string', $result['\Cake\Routing\Router::pathUrl(0)']['Bar::action']);
	}

}
