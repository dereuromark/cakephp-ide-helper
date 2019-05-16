<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Tools\Controller\Component\CommonComponent $Common
 */
class MyComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'Tools.Common'];

}
