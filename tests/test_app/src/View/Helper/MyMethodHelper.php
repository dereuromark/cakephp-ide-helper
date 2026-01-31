<?php
namespace TestApp\View\Helper;

use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\FormHelper $Form
 * @method bool isAdmin()
 * @method bool isStaff()
 */
class MyMethodHelper extends Helper {

	protected array $helpers = [
		'Html',
		'Form',
		'Shim.Configure',
		'SomeInvalidOneWillBeIgnored',
	];

}
