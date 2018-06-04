<?php
namespace App\Custom;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \App\Model\Table\BarBarsTable $BarBars
 * @property \App\Model\Table\FooTable $Foo
 */
class CustomClass {

	use ModelAwareTrait;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->loadModel('Foo');
	}

}
