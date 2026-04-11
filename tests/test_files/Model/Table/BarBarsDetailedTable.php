<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\FoosTable> $Foos
 * @property \Cake\ORM\Association\BelongsToMany<\Awesome\Model\Table\HousesTable> $Houses
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\TestApp\Model\Entity\BarBar> newEntities(array<array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar get(mixed $primaryKey, array<string, mixed>|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBar findOrCreate(\Cake\ORM\Query\SelectQuery<\TestApp\Model\Entity\BarBar>|callable|array<string, mixed> $search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $data, array<string, mixed> $options = [])
 * @method array<\TestApp\Model\Entity\BarBar> patchEntities(iterable<\TestApp\Model\Entity\BarBar> $entities, array<array<string, mixed>> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \TestApp\Model\Entity\BarBar>|false saveMany(iterable<\TestApp\Model\Entity\BarBar> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \TestApp\Model\Entity\BarBar> saveManyOrFail(iterable<\TestApp\Model\Entity\BarBar> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \TestApp\Model\Entity\BarBar>|false deleteMany(iterable<\TestApp\Model\Entity\BarBar> $entities, array<string, mixed> $options = [])
 * @method \Cake\Datasource\ResultSetInterface<int, \TestApp\Model\Entity\BarBar> deleteManyOrFail(iterable<\TestApp\Model\Entity\BarBar> $entities, array<string, mixed> $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 *
 * @extends \Cake\ORM\Table<array{My: \MyNamespace\MyPlugin\Model\Behavior\MyBehavior, Timestamp: \Cake\ORM\Behavior\TimestampBehavior}>
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
