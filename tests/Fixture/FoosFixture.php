<?php

namespace IdeHelper\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class FoosFixture extends TestFixture {

	/**
	 * Fields
	 */
	public array $fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'content' => ['type' => 'text', 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'params' => ['type' => 'json', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
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
			'params' => '[]',
		],
	];

}
