<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ModelTask;

class ModelTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ModelTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ModelTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(4, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\TableRegistry::get(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];
		$map = array_map(function ($className) {
			return (string)$className;
		}, $map);

		$expectedMap = [
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
			'MyNamespace/MyPlugin.My' => '\MyNamespace\MyPlugin\Model\Table\MyTable::class',
			'Relations.Bars' => '\Relations\Model\Table\BarsTable::class',
			'Relations.Foos' => '\Relations\Model\Table\FoosTable::class',
			'Relations.Users' => '\Relations\Model\Table\UsersTable::class',
			'SkipMe' => '\TestApp\Model\Table\SkipMeTable::class',
			'SkipSome' => '\TestApp\Model\Table\SkipSomeTable::class',
			'Wheels' => '\TestApp\Model\Table\WheelsTable::class',
			'WheelsExtra' => '\TestApp\Model\Table\WheelsExtraTable::class',
		];
		$this->assertSame($expectedMap, $map);
	}

}
