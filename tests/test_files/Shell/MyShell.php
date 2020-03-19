<?php
namespace TestApp\Shell;

use Cake\Console\Shell;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\CarsTable $Cars
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
