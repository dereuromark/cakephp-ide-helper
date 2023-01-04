<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \TestApp\Controller\Component\CheckHttpCacheComponent $CheckHttpCache
 * @property \Cake\Controller\Component\FormProtectionComponent $FormProtection
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class MyOtherComponent extends Component {

	public array $components = ['Flash', 'CheckHttpCache', 'SomeInvalidOneWillBeIgnored', 'FormProtection'];

}
