<?php

namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * Class AbstractTable
 */
abstract class AbstractTable extends Table
{
	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config)
	{
		parent::initialize($config);

		$this->addBehavior('Timestamp');
	}
}
