<?php
namespace TestApp\View\Helper;

use Cake\View\Helper;

class MyHelper extends Helper {

	protected array $helpers = [
		'Html',
		'Form',
		'Shim.Configure',
		'SomeInvalidOneWillBeIgnored',
	];

}
