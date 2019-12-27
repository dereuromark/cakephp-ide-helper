<?php
namespace Awesome\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Awesome\Model\Table\WindowsTable&\Cake\ORM\Association\HasMany $Windows
 */
class HousesTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->hasMany('Awesome.Windows');
	}

}
