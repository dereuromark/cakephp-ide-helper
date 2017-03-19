<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property \App\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $Cars
 * @method \App\Model\Entity\Wheeeeeeeel newEntity($data = null, array $options = [])
 */
class WheelsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Cars');
	}

}
