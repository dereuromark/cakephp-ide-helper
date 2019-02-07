<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @method \App\Model\Entity\Foo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Foo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Foo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Foo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Foo|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Foo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Foo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Foo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Tools\Model\Behavior\ConfirmableBehavior
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 */
class FooTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('BarBars');
		$this->belongsTo('Houses', [
			'className' => 'Awesome.Houses'
		]);
		$this->addBehavior('Tools.Confirmable');
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
