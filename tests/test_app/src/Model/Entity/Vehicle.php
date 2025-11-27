<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * Vehicle Entity for testing FieldConstantUsageTask.
 */
class Vehicle extends Entity {

	public const FIELD_ID = 'id';
	public const FIELD_NAME = 'name';
	public const FIELD_CONTENT = 'content';
	public const FIELD_CREATED = 'created';
	public const FIELD_MODIFIED = 'modified';

}
