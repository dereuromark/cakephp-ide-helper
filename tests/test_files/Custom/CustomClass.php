<?php
namespace TestApp\Custom;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 * @property \TestApp\Model\Table\FooTable $Foo
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
