<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Cake\Controller\Component\SecurityComponent Security
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class MyOtherComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'SomeInvalidOneWillBeIgnored', 'Security'];

}
