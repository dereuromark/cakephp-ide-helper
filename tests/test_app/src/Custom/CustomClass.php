<?php
namespace TestApp\Custom;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 */
class CustomClass {

	use ModelAwareTrait;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->loadModel('Foos');
	}

}
