<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 * @property \Awesome\Model\Table\WindowsTable $Windows
 * @property \Awesome\Model\Table\HousesTable $Houses
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
