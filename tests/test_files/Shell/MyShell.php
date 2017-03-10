<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 */
class MyShell extends Shell {

	/**
	 * @var string
	 */
	public $modelClass = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
