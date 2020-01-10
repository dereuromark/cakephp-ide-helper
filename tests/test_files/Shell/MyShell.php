<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 * @property \Cake\Shell\Task\CommandTask $Command
 */
class MyShell extends Shell {

	/**
	 * @var string
	 */
	protected $modelClass = 'Cars';

	/**
	 * @var array
	 */
	public $tasks = [
		'Command',
	];

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
