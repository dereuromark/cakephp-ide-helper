<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property \App\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $Cars
 * @property \App\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $CarsAwesome
 */
class SkipSomeTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('Cars');
	}

	/**
	 * @return void
	 */
	public function foo() {
		$this->CarsAwesome->doSthAwesome();
	}

}
