<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use App\Database\Type\UuidType;
use Cake\Database\Type;
use IdeHelper\Generator\Task\DatabaseTypeTask;
use Tools\TestSuite\TestCase;

class DatabaseTypeTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTypeTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->task = new DatabaseTypeTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		Type::set('uuid', new UuidType());

		$result = $this->task->collect();

		$this->assertCount(1, $result);

		$expected = '\Cake\Database\Type\BinaryType::class';
		$this->assertSame($expected, $result['\Cake\Database\Type::build(0)']['binary']);

		$expected = '\App\Database\Type\UuidType::class';
		$this->assertSame($expected, $result['\Cake\Database\Type::build(0)']['uuid']);
	}

}
