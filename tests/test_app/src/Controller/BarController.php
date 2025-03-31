<?php
namespace TestApp\Controller;

use TestApp\Model\Table\WheelsTable;

class BarController extends AppController {

	protected ?string $defaultTable = 'BarBars';

	protected WheelsTable $Wheels;

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('MyNamespace/MyPlugin.My');

		$this->Wheels = $this->fetchTable('Wheels');
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$this->paginate($this->BarBars);
	}

}
