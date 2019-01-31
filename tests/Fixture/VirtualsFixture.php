<?php
namespace IdeHelper\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class VirtualsFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
		],
	];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => 1,
		],
	];

}
