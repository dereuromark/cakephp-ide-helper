<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;
use TestApp\Model\Entity\Vehicle;

/**
 * VehiclesTable
 */
class VehiclesTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);
	}

	/**
	 * Example finder with string field names.
	 *
	 * @param \Cake\ORM\Query\SelectQuery $query
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function findActive($query) {
		return $query
			->select([Vehicle::FIELD_ID, Vehicle::FIELD_NAME, Vehicle::FIELD_CONTENT])
			->where([Vehicle::FIELD_NAME => 'test'])
			->orderBy([Vehicle::FIELD_CREATED => 'ASC']);
	}

	/**
	 * Another example with various query methods.
	 *
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function getByName(string $name) {
		return $this->find()
			->select(Vehicle::FIELD_NAME)
			->where([Vehicle::FIELD_NAME => $name])
			->andWhere([Vehicle::FIELD_CONTENT => 'active'])
			->orderByDesc(Vehicle::FIELD_MODIFIED)
			->groupBy(Vehicle::FIELD_NAME);
	}

}
