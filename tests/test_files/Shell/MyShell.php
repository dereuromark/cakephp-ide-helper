<?php
namespace TestApp\Shell;

use Cake\Console\Shell;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\CarsTable $Cars
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
