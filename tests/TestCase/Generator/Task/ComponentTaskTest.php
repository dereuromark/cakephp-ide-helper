<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\ComponentTask;
use Cake\TestSuite\TestCase;

class ComponentTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ComponentTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new ComponentTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		$expected = '\Cake\Controller\Component\AuthComponent::class';
		$this->assertSame($expected, $result['\Cake\Controller\Controller::loadComponent(0)']['Auth']);

		$expected = '\App\Controller\Component\RequestHandlerComponent::class';
		$this->assertSame($expected, $result['\Cake\Controller\Controller::loadComponent(0)']['RequestHandler']);

		$expected = '\Shim\Controller\Component\SessionComponent::class';
		$this->assertSame($expected, $result['\Cake\Controller\Controller::loadComponent(0)']['Shim.Session']);
	}

}
