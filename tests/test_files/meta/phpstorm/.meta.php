<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

	override(
		\ModelAwareTrait::loadModel(0),
		map([
			'BarBars' => \App\Model\Table\BarBarsTable::class,
			'Cars' => \App\Model\Table\CarsTable::class,
			'Exceptions' => \App\Model\Table\ExceptionsTable::class,
			'Foo' => \App\Model\Table\FooTable::class,
			'WheelsExtra' => \App\Model\Table\WheelsExtraTable::class,
			'Wheels' => \App\Model\Table\WheelsTable::class,
			'Awesome.Houses' => \Awesome\Model\Table\HousesTable::class,
			'Awesome.Windows' => \Awesome\Model\Table\WindowsTable::class,
		])
	);

}
