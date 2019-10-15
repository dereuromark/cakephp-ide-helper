<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use IdeHelper\Generator\Task\ModelTask;
use Tools\TestSuite\TestCase;

class ModelTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ModelTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->task = new ModelTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(3, $result);

		$expectedMap = [
			'Abstract' => '\App\Model\Table\AbstractTable::class',
			'BarBarsAbstract' => '\App\Model\Table\BarBarsAbstractTable::class',
			'BarBars' => '\App\Model\Table\BarBarsTable::class',
			'Callbacks' => '\App\Model\Table\CallbacksTable::class',
			'Cars' => '\App\Model\Table\CarsTable::class',
			'Exceptions' => '\App\Model\Table\ExceptionsTable::class',
			'Foo' => '\App\Model\Table\FooTable::class',
			'SkipMe' => '\App\Model\Table\SkipMeTable::class',
			'SkipSome' => '\App\Model\Table\SkipSomeTable::class',
			'WheelsExtra' => '\App\Model\Table\WheelsExtraTable::class',
			'Wheels' => '\App\Model\Table\WheelsTable::class',
			'Awesome.Houses' => '\Awesome\Model\Table\HousesTable::class',
			'Awesome.Windows' => '\Awesome\Model\Table\WindowsTable::class',
			'Controllers.Houses' => '\Controllers\Model\Table\HousesTable::class',
		];
		$map = array_shift($result);
		$this->assertSame($expectedMap, $map);
	}

}
