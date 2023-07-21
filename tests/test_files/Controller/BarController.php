<?php
namespace TestApp\Controller;

/**
 * @property \TestApp\Model\Table\WheelsTable $Wheels
 * @property \TestApp\Model\Table\BarBarsTable $BarBars
 * @property \MyNamespace\MyPlugin\Controller\Component\MyComponent $My
 *
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BarController extends AppController {

	protected ?string $defaultTable = 'BarBars';

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
