<?php
namespace TestApp\Command;

use Shim\Command\Command;

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
