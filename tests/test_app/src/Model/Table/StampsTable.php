<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;
use TestApp\Behavior\StampBehavior;

class StampsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->setTable('foos');
		$this->addBehavior(StampBehavior::class);
	}

}
