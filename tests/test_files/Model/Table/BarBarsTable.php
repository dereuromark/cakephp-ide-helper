<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[] newEntities(mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar get(mixed $primaryKey, string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBar findOrCreate($search, ?callable $callback = null, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[] patchEntities(iterable $entities, mixed[] $data, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false saveMany(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> saveManyOrFail(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false deleteMany(iterable $entities, mixed[] $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> deleteManyOrFail(iterable $entities, mixed[] $options = [])
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
