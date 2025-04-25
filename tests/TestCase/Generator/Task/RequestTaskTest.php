<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\RequestTask;
use Shim\TestSuite\TestCase;

class RequestTaskTest extends TestCase {

	protected ?RequestTask $task = null;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new RequestTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Http\ServerRequest::getParam()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'_ext' => "'_ext'",
			'_matchedRoute' => "'_matchedRoute'",
			'action' => "'action'",
			'controller' => "'controller'",
			'pass' => "'pass'",
			'plugin' => "'plugin'",
			'prefix' => "'prefix'",
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Http\ServerRequest::getAttribute(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\Cake\Http\Session::class';
		$this->assertSame($expected, (string)$map['session']);
	}

}
