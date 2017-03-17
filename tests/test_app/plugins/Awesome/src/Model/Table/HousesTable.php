<?php
namespace Awesome\Model\Table;

use Cake\ORM\Table;

class HousesTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->hasMany('Awesome.Windows');
	}

}
