<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\PluginTask;

class PluginTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\PluginTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new PluginTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Core\PluginApplicationInterface::addPlugin(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];
		$map = array_map(function ($className) {
			return (string)$className;
		}, $map);

		$expected = [
			'Bake' => '\Cake\Http\BaseApplication::class',
			'Cake/TwigView' => '\Cake\Http\BaseApplication::class',
			'Migrations' => '\Cake\Http\BaseApplication::class',
			'Shim' => '\Cake\Http\BaseApplication::class',
		];
		$this->assertSame($expected, $map);
	}

}
