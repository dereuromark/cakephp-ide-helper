<?php
namespace App\Model\Table;

/**
 * @property \App\Model\Table\FooTable&\Cake\ORM\Association\BelongsTo $Foo
 * @property \Awesome\Model\Table\HousesTable&\Cake\ORM\Association\BelongsToMany $Houses
 * @property \Awesome\Model\Table\WindowsTable&\Cake\ORM\Association\HasMany $Windows
 *
 * @method \App\Model\Entity\BarBarsAbstract newEmptyEntity()
 * @method \App\Model\Entity\BarBarsAbstract newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BarBarsAbstract get($primaryKey, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\BarBarsAbstract|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 */
class BarBarsAbstractTable extends AbstractTable {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->setTable('bar_bars');
		$this->belongsTo('Foo');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
