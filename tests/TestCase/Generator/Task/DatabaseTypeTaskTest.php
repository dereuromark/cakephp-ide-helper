<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Database\Type;
use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\DatabaseTypeTask;
use TestApp\Database\Type\UuidType;

class DatabaseTypeTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTypeTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new DatabaseTypeTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		Type::set('uuid', new UuidType());

		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Database\TypeFactory::build(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\Cake\Database\Type\BinaryType::class';
		$this->assertSame($expected, (string)$map['binary']);

		$expected = '\TestApp\Database\Type\UuidType::class';
		$this->assertSame($expected, (string)$map['uuid']);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Database\TypeFactory::map()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$this->assertSame('\'json\'', (string)$list['json']);
	}

}
