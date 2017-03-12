<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\BarBarsTable $BarBars
 */
class BarController extends AppController {

	/**
	 * @var string
	 */
	public $modelClass = 'BarBars';

	/**
	 * @var array
	 */
	public $components = ['Flash'];

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		$this->loadModel('Wheels');
	}

}
