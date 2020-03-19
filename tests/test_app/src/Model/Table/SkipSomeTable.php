<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \TestApp\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $Cars
 * @property \TestApp\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $CarsAwesome
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
