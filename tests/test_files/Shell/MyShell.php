<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 * @property \Cake\Shell\Task\AssetsTask $Assets
 */
class MyShell extends Shell {

	/**
	 * @var string
	 */
	public $modelClass = 'Cars';

	/**
	 * @var array
	 */
	public $tasks = [
		'Assets',
	];

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
