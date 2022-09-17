<?php

namespace IdeHelper\CodeCompletion\Task;

class ModelEventsTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\ORM';

	/**
	 * @return string
	 */
	public function type(): string {
		return static::TYPE_NAMESPACE;
	}

	/**
	 * @return string
	 */
	public function create(): string {
		return <<<HERE

        use ArrayObject;
        use Cake\Datasource\EntityInterface;
        use Cake\Event\EventInterface;
        use Cake\Validation\Validator;

        class Table
        {
            public function beforeMarshal(EventInterface \$event, ArrayObject \$data, ArrayObject \$options);
            public function afterMarshal(EventInterface \$event, EntityInterface \$entity, ArrayObject \$data, ArrayObject \$options);
            public function beforeFind(EventInterface \$event, Query \$query, ArrayObject \$options, \$primary);
            public function buildValidator(EventInterface \$event, Validator \$validator, \$name);
            public function buildRules(EventInterface \$event, RulesChecker \$rules);
            public function beforeRules(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options, \$operation);
            public function afterRules(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options, \$result, \$operation);
            public function beforeSave(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
            public function afterSave(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
            public function afterSaveCommit(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
            public function beforeDelete(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
            public function afterDelete(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
            public function afterDeleteCommit(EventInterface \$event, EntityInterface \$entity, ArrayObject \$options);
        }

        
        HERE;
	}

}
