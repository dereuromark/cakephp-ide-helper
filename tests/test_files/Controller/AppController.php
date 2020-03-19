<?php
namespace TestApp\Controller;

use Cake\Controller\Controller;

/**
 * @property \Tools\Controller\Component\CommonComponent $Common
 * @property \TestApp\Controller\Component\RequestHandlerComponent $RequestHandler
 */
class AppController extends Controller {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler'];

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		$this->loadComponent('Tools.Common');
	}

}
