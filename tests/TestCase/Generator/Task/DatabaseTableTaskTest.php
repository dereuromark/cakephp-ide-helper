<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\DatabaseTableTask;

class DatabaseTableTaskTest extends TestCase {

	/**
	 * @var array<string>
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
	protected function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new DatabaseTableTask();
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

		$this->assertCount(4, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertInstanceOf(RegisterArgumentsSet::class, $directive);
		$this->assertSame(DatabaseTableTask::SET_TABLE_NAMES, $directive->toArray()['set']);

		$list = $directive->toArray()['list'];
		$expectedList = [
			'cars' => "'cars'",
			'wheels' => "'wheels'",
		];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($value, (string)$list[$key]);
		}

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\AbstractMigration::table()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expectedList = [
			'argumentsSet(\'tableNames\')',
		];
		$this->assertSame($expectedList, $list);
	}

}
