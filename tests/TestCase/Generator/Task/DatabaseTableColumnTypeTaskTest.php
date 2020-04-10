<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\DatabaseTableColumnTypeTask;

class DatabaseTableColumnTypeTaskTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTableColumnTypeTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new DatabaseTableColumnTypeTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::addColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expectedList = [
			'text' => "'text'",
			'integer' => "'integer'",
		];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::changeColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}
	}

}
