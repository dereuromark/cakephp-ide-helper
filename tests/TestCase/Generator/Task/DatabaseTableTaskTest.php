<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\DatabaseTableTask;

class DatabaseTableTaskTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected DatabaseTableTask $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->fetchTable('Cars');
		$this->fetchTable('Wheels');

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

		$this->assertCount(5, $result);

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

		$expectedMethods = [
			'\Migrations\BaseMigration::table()',
			'\Migrations\BaseMigration::hasTable()',
			'\Migrations\BaseSeed::table()',
			'\Migrations\BaseSeed::hasTable()',
		];

		foreach ($expectedMethods as $expectedMethod) {
			/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
			$directive = array_shift($result);
			$this->assertSame($expectedMethod, $directive->toArray()['method']);

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

}
