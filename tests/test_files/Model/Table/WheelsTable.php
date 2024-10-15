<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @method \TestApp\Model\Entity\Wheel newEntity(array<mixed> $data, array<string, mixed> $options = [])
 * @property \Cake\ORM\Association\BelongsTo<\TestApp\Model\Table\CarsTable> $Cars
 * @method \TestApp\Model\Entity\Wheel newEmptyEntity()
 * @method \TestApp\Model\Entity\Wheel[] newEntities(array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel get(mixed $primaryKey, string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\Wheel findOrCreate($search, ?callable $callback = null, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel patchEntity(\Cake\Datasource\EntityInterface $entity, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel[] patchEntities(iterable<\Cake\Datasource\EntityInterface> $entities, array<mixed> $data, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel|false save(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel saveOrFail(\Cake\Datasource\EntityInterface $entity, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\Wheel>|false saveMany(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\Wheel> saveManyOrFail(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\Wheel>|false deleteMany(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface<\TestApp\Model\Entity\Wheel> deleteManyOrFail(iterable<\Cake\Datasource\EntityInterface> $entities, array<string, mixed> $options = [])
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class WheelsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('Cars');

		$this->addBehavior('Tree');
	}

}
