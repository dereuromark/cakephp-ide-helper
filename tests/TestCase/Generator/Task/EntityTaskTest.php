<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\EntityTask;

class EntityTaskTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\EntityTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new EntityTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(66, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertInstanceOf(RegisterArgumentsSet::class, $directive);
		$this->assertStringContainsString(EntityTask::SET_ENTITY_FIELDS, $directive->toArray()['set']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'content' => "'content'",
			'created' => "'created'",
			'id' => "'id'",
			'modified' => "'modified'",
			'name' => "'name'",
		];
		$this->assertSame($expected, $list);

		$directive = array_shift($result);
		$this->assertInstanceOf(ExpectedArguments::class, $directive);
	}

}
