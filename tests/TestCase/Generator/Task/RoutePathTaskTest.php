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
	protected function setUp(): void {
		parent::setUp();

		$this->task = new RoutePathTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(5, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertSame(RoutePathTask::SET_ROUTE_PATHS, $directive->toArray()['set']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($value) {
			return (string)$value;
		}, $list);

		$expected = [
			'Awesome.Admin/AwesomeHouses::openDoor' => "'Awesome.Admin/AwesomeHouses::openDoor'",
			'Bar::index' => "'Bar::index'",
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Routing\Router::pathUrl()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($value) {
			return (string)$value;
		}, $list);

		$expected = [
			'argumentsSet(\'routePaths\')',
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\Helper\UrlHelper::buildFromPath()', $directive->toArray()['method']);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\Helper\HtmlHelper::linkFromPath()', $directive->toArray()['method']);
	}

}
