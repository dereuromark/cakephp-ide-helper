<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\TableFinderTask;
use Shim\TestSuite\TestTrait;
use TestApp\Model\Table\CustomFinderTable;

class TableFinderTaskTest extends TestCase {

	use TestTrait;

	/**
	 * @var \IdeHelper\Generator\Task\TableFinderTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Configure::write('IdeHelper.preemptive', true);

		$this->task = new TableFinderTask();
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		Configure::delete('IdeHelper');

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(3, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\Table::find(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];
		$map = array_map(function ($className) {
			return (string)$className;
		}, $map);

		$expectedMap = [
			'all' => '\Cake\ORM\Query::class',
			'children' => '\Cake\ORM\Query::class',
			'list' => '\Cake\ORM\Query::class',
			'path' => '\Cake\ORM\Query::class',
			'somethingCustom' => '\Cake\ORM\Query::class',
			'threaded' => '\Cake\ORM\Query::class',
			'treeList' => '\Cake\ORM\Query::class',
		];
		$this->assertSame($expectedMap, $map);
	}

	/**
	 * @return void
	 */
	public function testAddMethod() {
		$result = [];

		$class = CustomFinderTable::class;
		$method = 'findSomethingCustom';

		/** @uses \IdeHelper\Generator\Task\TableFinderTask::addMethod() */
		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method, $class]);
		$this->assertSame(['somethingCustom'], $result);
	}

	/**
	 * @return void
	 */
	public function testAddMethodInvalid() {
		$result = [];

		$class = CustomFinderTable::class;

		$method = 'findBySomethingCustom';
		/** @uses \IdeHelper\Generator\Task\TableFinderTask::addMethod() */
		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method, $class]);
		$this->assertSame([], $result);

		$method = 'findSomethingCustomBySomethingElse';
		/** @uses \IdeHelper\Generator\Task\TableFinderTask::addMethod() */
		$result = $this->invokeMethod($this->task, 'addMethod', [$result, $method, $class]);
		$this->assertSame([], $result);
	}

}
