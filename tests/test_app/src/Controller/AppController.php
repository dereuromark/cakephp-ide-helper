<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * @property \Tools\Controller\Component\CommonOldComponent $Common
 */
class AppController extends Controller {

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('RequestHandler');
		$this->loadComponent('Tools.Common');
	}

}
