<?php
namespace TestApp\Shell;

use Cake\Console\Command;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\CarsTable $Cars
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
