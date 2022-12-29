<?php
namespace TestApp\Shell;

use Cake\Console\Shell;

class MyShell extends Shell {

	protected string $modelClass = 'Cars';

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
