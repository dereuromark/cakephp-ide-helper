<?php
namespace TestApp\Controller;

use Cake\Controller\Controller;

/**
 * @property \TestApp\Controller\Component\MyOtherComponent $MyOther
 */
class AppController extends Controller {

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('RequestHandler');
		$this->loadComponent('MyOther');
	}

}
