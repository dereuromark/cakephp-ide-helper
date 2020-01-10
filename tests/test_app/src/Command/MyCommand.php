<?php
namespace App\Shell;

use Cake\Console\Command;

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
