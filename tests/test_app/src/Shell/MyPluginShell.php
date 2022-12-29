<?php
namespace TestApp\Shell;

use Cake\Console\Shell;

/**
 */
class MyPluginShell extends Shell {

	protected string $modelClass = 'Awesome.Houses';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Awesome.Windows');
	}

}
