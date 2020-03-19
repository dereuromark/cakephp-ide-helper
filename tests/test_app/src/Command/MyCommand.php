<?php
namespace TestApp\Shell;

use Cake\Console\Command;

class MyCommand extends Command {

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
