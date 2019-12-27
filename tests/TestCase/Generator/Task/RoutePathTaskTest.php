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

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Routing\Router::pathUrl()', $directive->toArray()['method']);

		$map = $directive->toArray()['list'];
		$expected = [
			'Bar::action' => "'Bar::action'",
			'Controllers.Generic::action' => "'Controllers.Generic::action'",
			'Controllers.Houses::action' => "'Controllers.Houses::action'",
			'Foo::action' => "'Foo::action'",
		];

		$this->assertSame($expected, $map);
	}

}
