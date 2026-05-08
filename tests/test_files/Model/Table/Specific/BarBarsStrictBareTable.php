<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] newEntities(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBar findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\TestApp\Model\Entity\BarBar $entity, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] patchEntities(iterable<\TestApp\Model\Entity\BarBar> $entities, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\TestApp\Model\Entity\BarBar $entity, array $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\TestApp\Model\Entity\BarBar $entity, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false saveMany(iterable<\TestApp\Model\Entity\BarBar> $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> saveManyOrFail(iterable<\TestApp\Model\Entity\BarBar> $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false deleteMany(iterable<\TestApp\Model\Entity\BarBar> $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> deleteManyOrFail(iterable<\TestApp\Model\Entity\BarBar> $entities, array $options = [])
 * @method bool delete(\TestApp\Model\Entity\BarBar $entity, array $options = [])
 * @method bool deleteOrFail(\TestApp\Model\Entity\BarBar $entity, array $options = [])
 * @method \TestApp\Model\Entity\BarBar|array<\TestApp\Model\Entity\BarBar> loadInto(\TestApp\Model\Entity\BarBar|array<\TestApp\Model\Entity\BarBar> $entities, array $contain)
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
