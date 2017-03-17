<?php
namespace App\Controller;

class BarController extends AppController {

	/**
	 * @var string
	 */
	public $modelClass = 'BarBars';

	/**
	 * @var array
	 */
	public $components = ['Flash', 'Shim.Session'];

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		$this->loadModel('Wheels');
	}

}
