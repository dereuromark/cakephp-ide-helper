<?php

namespace TestApp\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SmallWindowsFixture extends TestFixture {

	/**
	 * Fields
	 */
	public array $fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
		],
		'_options' => [
			'engine' => 'InnoDB',
			'collation' => 'utf8_general_ci',
		],
	];

	/**
	 * Records
	 */
	public array $records = [
		[
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
		],
	];

}
