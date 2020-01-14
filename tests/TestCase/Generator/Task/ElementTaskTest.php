<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ElementTask;
use Shim\TestSuite\TestTrait;

class ElementTaskTest extends TestCase {

	use TestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\ElementTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

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

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\View\View::element(0)', $directive->toArray()['method']);

		$expectedMap = [
			'Awesome.pagination' => '\Cake\View\View::class',
			'deeply/nested' => '\Cake\View\View::class',
			'example' => '\Cake\View\View::class',
		];
		$this->assertSame($expectedMap, $directive->toArray()['map']);
	}

}
