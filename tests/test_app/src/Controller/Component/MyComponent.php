<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;

class MyComponent extends Component {

	public array $components = ['Flash', 'CheckHttpCache', 'MyNamespace/MyPlugin.My'];

}
