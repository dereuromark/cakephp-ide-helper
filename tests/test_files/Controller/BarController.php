<?php
namespace TestApp\Controller;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 * @property \Shim\Controller\Component\SessionComponent $Session
 *
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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

	/**
	 * @return \Cake\Http\Response|void
	 */
	public function index() {
		$query = $this->paginate($this->BarBars);
	}

}
