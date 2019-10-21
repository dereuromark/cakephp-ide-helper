<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\TableAssociationTask;

class TableAssociationTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\TableAssociationTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new TableAssociationTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(4, $result);

		$expectedMap = [
			'Abstract' => '\Cake\ORM\Association\BelongsTo::class',
			'BarBarsAbstract' => '\Cake\ORM\Association\BelongsTo::class',
			'BarBars' => '\Cake\ORM\Association\BelongsTo::class',
			'Callbacks' => '\Cake\ORM\Association\BelongsTo::class',
			'Cars' => '\Cake\ORM\Association\BelongsTo::class',
			'CustomFinder' => '\Cake\ORM\Association\BelongsTo::class',
			'Exceptions' => '\Cake\ORM\Association\BelongsTo::class',
			'Foo' => '\Cake\ORM\Association\BelongsTo::class',
			'SkipMe' => '\Cake\ORM\Association\BelongsTo::class',
			'SkipSome' => '\Cake\ORM\Association\BelongsTo::class',
			'WheelsExtra' => '\Cake\ORM\Association\BelongsTo::class',
			'Wheels' => '\Cake\ORM\Association\BelongsTo::class',
			'Awesome.Houses' => '\Cake\ORM\Association\BelongsTo::class',
			'Awesome.Windows' => '\Cake\ORM\Association\BelongsTo::class',
			'Controllers.Houses' => '\Cake\ORM\Association\BelongsTo::class',
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

}
