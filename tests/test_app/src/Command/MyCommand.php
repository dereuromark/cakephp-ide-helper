<?php
namespace TestApp\Command;

use Shim\Command\Command;

class MyCommand extends Command {

	protected ?string $defaultTable = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->fetchTable('Wheels');
	}

}
