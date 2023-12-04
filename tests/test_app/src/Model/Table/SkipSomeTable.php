<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\CarsTable> $Cars
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\CarsTable> $CarsAwesome
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
