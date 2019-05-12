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
	public function setUp(): void {
		parent::setUp();

		$this->task = new PluginTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		$expectedMap = [
			'Bake' => '\Cake\Http\BaseApplication::class',
			'Shim' => '\Cake\Http\BaseApplication::class',
			'Tools' => '\Cake\Http\BaseApplication::class',
			'WyriHaximus/TwigView' => '\Cake\Http\BaseApplication::class',
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

}
