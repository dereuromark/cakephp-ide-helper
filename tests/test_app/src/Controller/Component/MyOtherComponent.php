<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \TestApp\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Cake\Controller\Component\SecurityComponent Security
 */
class MyOtherComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'SomeInvalidOneWillBeIgnored', 'Security'];

}
