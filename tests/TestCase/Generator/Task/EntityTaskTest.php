<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Generator\Task\EntityTask;

class EntityTaskTest extends TestCase {

	/**
	 * @var array<string>
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
	protected function setUp(): void {
		parent::setUp();

		$this->task = new EntityTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(88, $result);

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
			'foo' => "'foo'",
			'houses' => "'houses'",
			'id' => "'id'",
			'name' => "'name'",
		];
		$this->assertSame($expected, $list);

		$directive = array_shift($result);
		$this->assertInstanceOf(ExpectedArguments::class, $directive);
	}

}
