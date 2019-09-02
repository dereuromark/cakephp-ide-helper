<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class BarBarsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Foo');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('Tools.Confirmable');
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
