<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\DatabaseTableTask;

class DatabaseTableTaskTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTableTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new DatabaseTableTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\AbstractMigration::table()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expectedList = [
			'cars' => "'Cars'",
			'wheels' => "'Wheels'",
		];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}
	}

}
