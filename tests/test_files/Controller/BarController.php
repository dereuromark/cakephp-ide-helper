<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\BarBarsTable $BarBars
 * @property \Tools\Controller\Component\MobileComponent $Mobile
 *
 * @method \App\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BarController extends AppController {

	/**
	 * @var string
	 */
	public $modelClass = 'BarBars';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('Tools.Mobile');

		$this->loadModel('Wheels');
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$this->paginate($this->BarBars);
	}

}
