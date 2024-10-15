<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[] newEntities(array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar get(mixed $primaryKey, string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBar findOrCreate($search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[] patchEntities(iterable<\Cake\Datasource\EntityInterface> $entities, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false saveMany(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> saveManyOrFail(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar>|false deleteMany(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\BarBar> deleteManyOrFail(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
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
