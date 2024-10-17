<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

/**
 * @method \TestApp\Model\Entity\Foo get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \TestApp\Model\Entity\Foo newEntity($data = null, array $options = [])
 * @method \TestApp\Model\Entity\Foo[] newEntities(array $data, array $options = [])
 * @method \TestApp\Model\Entity\Foo|false save(\TestApp\Model\Entity\Foo $entity, array $options = [])
 * @method \TestApp\Model\Entity\Foo saveOrFail(\TestApp\Model\Entity\Foo $entity, array $options = [])
 * @method \TestApp\Model\Entity\Foo patchEntity(\TestApp\Model\Entity\Foo $entity, array $data, array $options = [])
 * @method \TestApp\Model\Entity\Foo[] patchEntities($entities, array $data, array $options = [])
 * @method \TestApp\Model\Entity\Foo findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, callable $callback = null, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 */
class FooTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('BarBars');
		$this->belongsTo('Houses', [
			'className' => 'Awesome.Houses'
		]);
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
