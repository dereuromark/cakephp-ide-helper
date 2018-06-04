<?php
namespace App\Custom\Nested;

use Cake\Datasource\ModelAwareTrait;

class NestedClass {

	use ModelAwareTrait;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->loadModel('Foo');
	}

}
