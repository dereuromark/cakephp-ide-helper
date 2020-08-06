<?php
namespace TestApp\View\Helper;

use Cake\View\Helper;

class MyHelper extends Helper {

	/**
	 * @var array
	 */
	protected $helpers = [
		'Html',
		'Form',
		'Shim.Configure',
		'SomeInvalidOneWillBeIgnored',
	];

}
