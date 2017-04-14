<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property \App\Model\Table\CarsOldTable|\Cake\ORM\Association\BelongsTo $Cars
 */
class WheelsExtraTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->table('wheels');
		$this->belongsTo('Cars');
	}

}
