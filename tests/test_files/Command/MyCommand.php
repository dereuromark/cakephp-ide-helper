<?php
namespace TestApp\Command;

use Shim\Command\Command;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\CarsTable $Cars
 */
class MyCommand extends Command {

	protected ?string $defaultTable = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->fetchTable('Wheels');
	}

}
