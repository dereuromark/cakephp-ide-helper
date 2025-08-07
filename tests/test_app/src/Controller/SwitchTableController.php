<?php
namespace TestApp\Controller;

/**
 * @property \TestApp\Model\Table\OldTablesTable $OldTables
 */
class SwitchTableController extends AppController {

	protected ?string $defaultTable = 'NewTables';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$this->paginate($this->NewTables);
	}

}