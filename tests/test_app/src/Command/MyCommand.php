<?php
namespace TestApp\Command;

use Shim\Command\Command;

class MyCommand extends Command {

	protected string $modelClass = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
