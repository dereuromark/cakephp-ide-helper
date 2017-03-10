<?php
namespace App\Shell;

use Cake\Console\Shell;

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
