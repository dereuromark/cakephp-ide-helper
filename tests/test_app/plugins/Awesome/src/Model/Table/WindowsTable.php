<?php
namespace Awesome\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Awesome\Model\Table\HousesTable&\Cake\ORM\Association\BelongsTo $Houses
 */
class WindowsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('Awesome.Houses');
	}

}
