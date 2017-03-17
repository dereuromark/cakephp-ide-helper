<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ExceptionsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->setTable('invalid');

		$this->belongsTo('TotallyInvalid');
	}

}
