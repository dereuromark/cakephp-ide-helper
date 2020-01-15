<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * @property \App\Controller\Component\MyOtherComponent $MyOther
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
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
