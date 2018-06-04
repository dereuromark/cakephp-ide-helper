<?php
namespace App\Custom\Nested;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \App\Model\Table\FooTable $Foo
 */
class NestedClass {

	use ModelAwareTrait;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->loadModel('Foo');
	}

}
