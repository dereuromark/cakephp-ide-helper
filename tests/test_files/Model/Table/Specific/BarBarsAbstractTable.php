<?php
namespace TestApp\Model\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBarsAbstract newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBarsAbstract newEntity(array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[] newEntities(array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract get(mixed $primaryKey, string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBarsAbstract findOrCreate($search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract patchEntity(\TestApp\Model\Entity\BarBarsAbstract $entity, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract[] patchEntities(iterable<\TestApp\Model\Entity\BarBarsAbstract> $entities, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract|false save(\TestApp\Model\Entity\BarBarsAbstract $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBarsAbstract saveOrFail(\TestApp\Model\Entity\BarBarsAbstract $entity, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract>|false saveMany(iterable<\TestApp\Model\Entity\BarBarsAbstract> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract> saveManyOrFail(iterable<\TestApp\Model\Entity\BarBarsAbstract> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract>|false deleteMany(iterable<\TestApp\Model\Entity\BarBarsAbstract> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBarsAbstract> deleteManyOrFail(iterable<\TestApp\Model\Entity\BarBarsAbstract> $entities, array<string, mixed> $options = [])
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
