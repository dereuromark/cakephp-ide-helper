<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $virtual_one
 * @property \App\Model\Entity\Wheel[] $wheels
 */
class Wheel extends Entity {

	/**
	 * @var array
	 */
	protected $_virtual = [
		'virtual_one',
	];

	/**
	 * @return string|null
	 */
	protected function _getVirtualOne() {
		return 'Virtual One';
	}

	const FIELD_ID = 'id';
	const FIELD_NAME = 'name';
	const FIELD_CONTENT = 'content';
	const FIELD_CREATED = 'created';
	const FIELD_MODIFIED = 'modified';
	const FIELD_VIRTUAL_ONE = 'virtual_one';
	const FIELD_WHEELS = 'wheels';

}
