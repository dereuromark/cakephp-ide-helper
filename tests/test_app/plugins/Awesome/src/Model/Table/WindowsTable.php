<?php
namespace Awesome\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\Awesome\Model\Table\HousesTable> $Houses
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
