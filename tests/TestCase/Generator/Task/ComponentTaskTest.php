<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ComponentTask;

class ComponentTaskTest extends TestCase {

	protected ComponentTask $task;

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

		$expected = '\Cake\Controller\Component\FormProtectionComponent::class';
		$this->assertSame($expected, (string)$map['FormProtection']);

		$expected = '\TestApp\Controller\Component\MyOtherComponent::class';
		$this->assertSame($expected, (string)$map['MyOther']);
	}

}
