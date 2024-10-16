<?php
namespace TestApp\Model\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBarsAbstract newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBarsAbstract newEntity(mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[] newEntities(mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract get(mixed $primaryKey, string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBarsAbstract findOrCreate($search, ?callable $callback = null, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract patchEntity(\Cake\Datasource\EntityInterface $entity, mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[] patchEntities(iterable $entities, mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract|false save(\Cake\Datasource\EntityInterface $entity, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract saveOrFail(\Cake\Datasource\EntityInterface $entity, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract>|false saveMany(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract> saveManyOrFail(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract>|false deleteMany(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract> deleteManyOrFail(iterable $entities, mixed[] $options = [])
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
		$this->belongsTo('Foos');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
