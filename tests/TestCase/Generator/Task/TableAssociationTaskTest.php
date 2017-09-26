<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Core\Configure;
use IdeHelper\Generator\Task\TableAssociationTask;
use Tools\TestSuite\TestCase;

class TableAssociationTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\TableAssociationTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		Configure::write('IdeHelper.preemptive', true);

		$this->task = new TableAssociationTask();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		Configure::delete('IdeHelper');

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(4, $result);

		$expectedMap = [
			'BarBars' => '\Cake\ORM\Association\BelongsTo::class',
			'Cars' => '\Cake\ORM\Association\BelongsTo::class',
			'Exceptions' => '\Cake\ORM\Association\BelongsTo::class',
			'Foo' => '\Cake\ORM\Association\BelongsTo::class',
			'WheelsExtra' => '\Cake\ORM\Association\BelongsTo::class',
			'Wheels' => '\Cake\ORM\Association\BelongsTo::class',
			'Awesome.Houses' => '\Cake\ORM\Association\BelongsTo::class',
			'Awesome.Windows' => '\Cake\ORM\Association\BelongsTo::class',
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

}
