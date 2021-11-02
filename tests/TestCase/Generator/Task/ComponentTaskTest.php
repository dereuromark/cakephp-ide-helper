<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ComponentTask;

class ComponentTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ComponentTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ComponentTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Controller\Controller::loadComponent(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\Cake\Controller\Component\AuthComponent::class';
		$this->assertSame($expected, (string)$map['Auth']);

		$expected = '\TestApp\Controller\Component\RequestHandlerComponent::class';
		$this->assertSame($expected, (string)$map['RequestHandler']);
	}

}
