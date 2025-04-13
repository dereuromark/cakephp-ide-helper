<?php
namespace TestApp\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;

class CustomFinderTable extends Table {

	/**
	 * @param \Cake\ORM\Query\SelectQuery $query
	 *
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function findSomethingCustom(SelectQuery $query): SelectQuery {
		return $query;
	}

}
