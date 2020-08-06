<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;

class MyComponent extends Component {

	/**
	 * @var array
	 */
	protected $components = ['Flash', 'RequestHandler', 'MyNamespace/MyPlugin.My'];

}
