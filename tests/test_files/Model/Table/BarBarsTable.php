<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \TestApp\Model\Table\FooTable&\Cake\ORM\Association\BelongsTo $Foo
 * @property \Awesome\Model\Table\HousesTable&\Cake\ORM\Association\BelongsToMany $Houses
 * @property \Awesome\Model\Table\WindowsTable&\Cake\ORM\Association\HasMany $Windows
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] newEntities(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar get($primaryKey, $options = [])
 * @method \TestApp\Model\Entity\BarBar findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
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

		$this->belongsTo('Foo');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
