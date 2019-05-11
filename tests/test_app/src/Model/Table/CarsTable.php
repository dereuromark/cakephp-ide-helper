<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class CarsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->hasMany('Wheels');
	}

}
