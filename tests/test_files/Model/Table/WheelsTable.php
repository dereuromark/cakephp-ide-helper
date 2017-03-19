<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property \App\Model\Table\CarsTable|\Cake\ORM\Association\BelongsTo $Cars
 * @method \App\Model\Entity\Wheel newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Wheel get($primaryKey, $options = [])
 * @method \App\Model\Entity\Wheel[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Wheel|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Wheel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Wheel[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Wheel findOrCreate($search, callable $callback = null, $options = [])
 */
class WheelsTable extends Table {

	/**
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Cars');
	}

}
