<?php
namespace TestApp\Custom;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \TestApp\Model\Table\FoosTable $Foos
 */
class CustomClass {

	use ModelAwareTrait;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->fetchModel('Foos');
	}

}
