<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property-read string|null $virtual_one
 * @property mixed $virtual_two
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 */
class Wheel extends Entity {

	const OTHER_NAME = 'unrelated';

	const FIELD_ID = 'id';
	const FIELD_NAME = 'name';
	const FIELD_CONTENT = 'content';
	const FIELD_CREATED = 'created';
	const FIELD_WHEELS = 'wheels';
	const FIELD_VIRTUAL_TWO = 'virtual_two';
	const FIELD_MODIFIED = 'modified';
	const FIELD_VIRTUAL_ONE = 'virtual_one';

	protected array $_virtual = [
		'virtual_one',
	];

	/**
	 * @return string|null
	 */
	protected function _getVirtualOne() {
		return 'Virtual One';
	}

	protected function _getVirtualTwo() {
		// Missing return type and docblock means mixed as result
		return 'Virtual Two';
	}

	/**
	 * @param \TestApp\Model\Entity\Wheel[] $wheels
	 *
	 * @return \TestApp\Model\Entity\Wheel[]
	 */
	protected function _getWheels($wheels = []) {
		return $wheels;
	}

}
