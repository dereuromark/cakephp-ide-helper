<?php
namespace App\Shell;

use Cake\Console\Command;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 */
class MyCommand extends Command {

	/**
	 * @var string
	 */
	protected $modelClass = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
