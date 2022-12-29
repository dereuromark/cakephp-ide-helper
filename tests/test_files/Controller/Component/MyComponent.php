<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \TestApp\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \MyNamespace\MyPlugin\Controller\Component\MyComponent $My
 */
class MyComponent extends Component {

	public array $components = ['Flash', 'RequestHandler', 'MyNamespace/MyPlugin.My'];

}
