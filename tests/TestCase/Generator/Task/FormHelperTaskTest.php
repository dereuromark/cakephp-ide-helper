<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Task\FormHelperTask;

class FormHelperTaskTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\FormHelperTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new FormHelperTask();
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();

		unset($this->task);
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertInstanceOf(ExpectedArguments::class, $directive);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expectedList = [
			'content' => "'content'",
			'created' => "'created'",
			'id' => "'id'",
			'modified' => "'modified'",
			'name' => "'name'",
			'user_id' => "'user_id'",
		];
		$this->assertSame($expectedList, $list);
	}

}
