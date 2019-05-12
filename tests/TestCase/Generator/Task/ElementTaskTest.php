<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ElementTask;
use Tools\TestSuite\ToolsTestTrait;

class ElementTaskTest extends TestCase {

	use ToolsTestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\ElementTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		//Configure::write('IdeHelper.preemptive', true);

		$this->task = new ElementTask();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$expectedMap = [
			'deeply/nested' => '\Cake\View\View::class',
			'example' => '\Cake\View\View::class'
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

}
