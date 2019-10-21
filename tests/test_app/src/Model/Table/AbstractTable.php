<?php

namespace App\Model\Table;

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
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->addBehavior('Timestamp');
	}
}
