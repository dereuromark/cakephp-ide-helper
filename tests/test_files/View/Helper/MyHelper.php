<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * @property \App\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Shim\View\Helper\ConfigureHelper $Configure
 */
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
