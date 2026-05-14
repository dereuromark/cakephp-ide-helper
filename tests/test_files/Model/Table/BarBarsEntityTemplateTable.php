<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @extends \Cake\ORM\Table<array{My: \MyNamespace\MyPlugin\Model\Behavior\MyBehavior, Timestamp: \Cake\ORM\Behavior\TimestampBehavior}, \TestApp\Model\Entity\BarBar>
 *
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false saveMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> saveManyOrFail(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false deleteMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 */
class BarBarsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('Foos');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
