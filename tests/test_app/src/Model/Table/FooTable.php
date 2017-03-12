<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class FooTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->addBehavior('Tools.Confirmable');
		$this->addBehavior('Timestamp');
	}

}
