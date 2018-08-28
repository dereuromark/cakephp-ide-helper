<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Table;

class CallbacksTable extends Table {

	/**
	 * @param \Cake\Event\Event $event Event
	 * @param \App\Model\Entity\Callback $entity Entity
	 * @param \ArrayObject $options Options
	 * @return void
	 */
	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
	}

	/**
	 * @param \Cake\Event\EventInterface $event
	 * @param \App\Model\Entity\Callback $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
	}

	public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options) {
	}

}
