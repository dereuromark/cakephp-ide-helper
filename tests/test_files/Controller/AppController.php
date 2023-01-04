<?php
namespace TestApp\Controller;

use Cake\Controller\Controller;

/**
 * @property \TestApp\Controller\Component\MyOtherComponent $MyOther
 * @property \TestApp\Controller\Component\CheckHttpCacheComponent $CheckHttpCache
 */
class AppController extends Controller {

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('CheckHttpCache');
		$this->loadComponent('MyOther');
	}

}
