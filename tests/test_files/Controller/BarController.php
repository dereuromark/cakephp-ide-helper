<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\BarBarsTable $BarBars
 * @property \Shim\Controller\Component\SessionComponent $Session
 */
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
