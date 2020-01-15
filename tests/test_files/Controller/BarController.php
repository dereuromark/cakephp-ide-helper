<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\BarBarsTable $BarBars
 * @property \MyNamespace\MyPlugin\Controller\Component\MyComponent $My
 *
 * @method \App\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BarController extends AppController {

	/**
	 * @var string
	 */
	protected $modelClass = 'BarBars';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('MyNamespace/MyPlugin.My');

		$this->loadModel('Wheels');
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$this->paginate($this->BarBars);
	}

}
