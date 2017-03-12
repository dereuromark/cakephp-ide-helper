<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class MyComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'Shim.Session'];

}
