<?php
namespace TestApp\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;

class CustomFinderTable extends Table {

	/**
	 * @param \Cake\ORM\Query $query
	 *
	 * @return \Cake\ORM\Query
	 */
	public function findSomethingCustom(SelectQuery $query): SelectQuery {
		return $query;
	}

}
