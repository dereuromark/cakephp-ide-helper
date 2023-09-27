<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @method \TestApp\Model\Entity\Wheel newEntity(array $data, array $options = [])
 * @property \TestApp\Model\Table\CarsTable&\Cake\ORM\Association\BelongsTo $Cars
 * @method \TestApp\Model\Entity\Wheel newEmptyEntity()
 * @method \TestApp\Model\Entity\Wheel[] newEntities(array $data, array $options = [])
 * @method \TestApp\Model\Entity\Wheel get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\Wheel findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \TestApp\Model\Entity\Wheel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \TestApp\Model\Entity\Wheel[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \TestApp\Model\Entity\Wheel|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \TestApp\Model\Entity\Wheel saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\Wheel[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, array $options = [])
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
