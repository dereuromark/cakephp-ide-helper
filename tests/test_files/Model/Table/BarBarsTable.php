<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @property \TestApp\Model\Table\FoosTable&\Cake\ORM\Association\BelongsTo $Foos
 * @property \Awesome\Model\Table\HousesTable&\Cake\ORM\Association\BelongsToMany $Houses
 * @property \Awesome\Model\Table\WindowsTable&\Cake\ORM\Association\HasMany $Windows
 *
 * @method \TestApp\Model\Entity\BarBar newEmptyEntity()
 * @method \TestApp\Model\Entity\BarBar newEntity(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] newEntities(array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\BarBar findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \TestApp\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \TestApp\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \TestApp\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, array $options = [])
 * @method \TestApp\Model\Entity\BarBar[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, array $options = [])
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
