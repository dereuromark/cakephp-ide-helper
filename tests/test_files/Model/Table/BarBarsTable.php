<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property \App\Model\Table\FooTable&\Cake\ORM\Association\BelongsTo $Foo
 * @property \Awesome\Model\Table\HousesTable&\Cake\ORM\Association\BelongsToMany $Houses
 * @property \Awesome\Model\Table\WindowsTable&\Cake\ORM\Association\HasMany $Windows
 *
 * @method \App\Model\Entity\BarBar get($primaryKey, $options = [])
 * @method \App\Model\Entity\BarBar newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BarBar[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BarBar|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BarBar saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BarBar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BarBar[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BarBar findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Tools\Model\Behavior\ConfirmableBehavior
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
 */
class BarBarsTable extends Table {

	public function initialize(array $config): void {
		parent::initialize($config);

		$this->belongsTo('Foo');
		$this->belongsToMany('Houses', [
			'className' => 'Awesome.Houses',
			'through' => 'Awesome.Windows',
		]);
		$this->addBehavior('Tools.Confirmable');
		$this->addBehavior('Timestamp');
		$this->addBehavior('MyNamespace/MyPlugin.My');
	}

}
