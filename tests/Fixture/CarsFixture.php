<?php

namespace IdeHelper\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use TestApp\Model\Enum\CarStatus;

class CarsFixture extends TestFixture {

	/**
	 * Fields
	 */
	public array $fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'content' => ['type' => 'text', 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'status' => ['type' => 'tinyinteger', 'length' => 2, 'null' => false, 'default' => '0'],
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
			'content' => 'Lorem ipsum dolor sit amet',
			'created' => '2016-06-23 14:59:54',
			'modified' => '2016-06-23 14:59:54',
			'status' => 0, //CarStatus::NEW,
		],
	];

}
