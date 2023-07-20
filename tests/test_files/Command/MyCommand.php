<?php
namespace TestApp\Command;

use Shim\Command\Command;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\CarsTable $Cars
 */
class MyCommand extends Command {

	protected ?string $defaultTable = 'Cars';

	/** @var \Relations\Model\Table\BarsTable */
	protected $Bars;

	/**
	 * @return void
	 */
	public function main() {
		$this->Wheels = $this->fetchTable('Wheels');
		$this->Bars = $this->fetchTable('Relations.Bars');
	}

}
