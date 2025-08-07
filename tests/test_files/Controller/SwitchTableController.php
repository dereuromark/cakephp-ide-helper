<?php
namespace TestApp\Controller;

/**
 * @property \TestApp\Model\Table\NewTablesTable $NewTables
 * @method \TestApp\Model\Entity\NewTable[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\NewTable> paginate(\Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object = null, array $settings = [])
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