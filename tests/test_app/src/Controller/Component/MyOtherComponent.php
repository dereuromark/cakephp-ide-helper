<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 */
class MyOtherComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'SomeInvalidOneWillBeIgnored'];

}
