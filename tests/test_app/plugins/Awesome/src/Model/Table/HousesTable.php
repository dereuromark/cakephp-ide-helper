<?php
namespace Awesome\Model\Table;

use Cake\ORM\Table;

/**
 * @extends \Cake\ORM\Table<array{}, \Cake\ORM\Entity>
 *
 * @property \Cake\ORM\Association\HasMany<\Awesome\Model\Table\WindowsTable> $Windows
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
