<?php
namespace TestApp\Model\Table;

use Cake\Database\Type\EnumType;
use Cake\ORM\Table;
use TestApp\Model\Enum\CarStatus;

class CarsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->hasMany('Wheels');

		$this->getSchema()->setColumnType('status', EnumType::from(CarStatus::class));
	}

}
