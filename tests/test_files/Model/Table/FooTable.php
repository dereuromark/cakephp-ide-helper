<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @method \App\Model\Entity\Foo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Foo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Foo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Foo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Foo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Foo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Foo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Tools\Model\Behavior\ConfirmableBehavior
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FooTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->addBehavior('Tools.Confirmable');
		$this->addBehavior('Timestamp');
	}

}
