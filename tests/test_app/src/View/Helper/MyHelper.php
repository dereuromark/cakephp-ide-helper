<?php
namespace App\View\Helper;

use Cake\View\Helper;

class MyHelper extends Helper {

	/**
	 * @var array
	 */
	public $helpers = [
		'Html',
		'Form',
		'Shim.Configure',
		'SomeInvalidOneWillBeIgnored',
	];

}
