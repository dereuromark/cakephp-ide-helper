<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Task\FormHelperTask;

class FormHelperTaskTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected FormHelperTask $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['Awesome', 'Controllers', 'MyNamespace/MyPlugin', 'Relations', 'Shim', 'IdeHelper']);
		$this->fetchTable('Cars');
		$this->fetchTable('Wheels');

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
			'params' => "'params'",
			'status' => "'status'",
			'user_id' => "'user_id'",
		];
		$this->assertSame($expectedList, $list);
	}

}
