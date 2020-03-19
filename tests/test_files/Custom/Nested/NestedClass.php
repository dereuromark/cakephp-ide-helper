<?php
namespace TestApp\Custom\Nested;

use Cake\Datasource\ModelAwareTrait;

/**
 * @property \TestApp\Model\Table\FooTable $Foo
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
