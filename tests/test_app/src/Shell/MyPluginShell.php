<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 */
class MyPluginShell extends Shell {

	/**
	 * @var string
	 */
	public $modelClass = 'Awesome.Houses';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Awesome.Windows');
	}

}
