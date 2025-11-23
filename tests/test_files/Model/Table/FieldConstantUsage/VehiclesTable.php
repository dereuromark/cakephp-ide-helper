<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

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
			->select(['id', 'name', 'content'])
			->where(['name' => 'test'])
			->orderBy(['created' => 'ASC']);
	}

	/**
	 * Another example with various query methods.
	 *
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function getByName(string $name) {
		return $this->find()
			->select('name')
			->where(['name' => $name])
			->andWhere(['content' => 'active'])
			->orderByDesc('modified')
			->groupBy('name');
	}

}
