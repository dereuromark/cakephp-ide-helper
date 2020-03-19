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

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\TableRegistry::get(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expectedMap = [
			'Abstract' => '\TestApp\Model\Table\AbstractTable::class',
			'Awesome.Houses' => '\Awesome\Model\Table\HousesTable::class',
			'Awesome.Windows' => '\Awesome\Model\Table\WindowsTable::class',
			'BarBars' => '\TestApp\Model\Table\BarBarsTable::class',
			'BarBarsAbstract' => '\TestApp\Model\Table\BarBarsAbstractTable::class',
			'Callbacks' => '\TestApp\Model\Table\CallbacksTable::class',
			'Cars' => '\TestApp\Model\Table\CarsTable::class',
			'Controllers.Houses' => '\Controllers\Model\Table\HousesTable::class',
			'CustomFinder' => '\TestApp\Model\Table\CustomFinderTable::class',
			'Exceptions' => '\TestApp\Model\Table\ExceptionsTable::class',
			'Foo' => '\TestApp\Model\Table\FooTable::class',
			'SkipMe' => '\TestApp\Model\Table\SkipMeTable::class',
			'SkipSome' => '\TestApp\Model\Table\SkipSomeTable::class',
			'Wheels' => '\TestApp\Model\Table\WheelsTable::class',
			'WheelsExtra' => '\TestApp\Model\Table\WheelsExtraTable::class',
		];
		$this->assertSame($expectedMap, $map);
	}

}
