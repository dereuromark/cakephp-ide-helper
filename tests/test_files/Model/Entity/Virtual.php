<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property-read bool $virtual_read_only This should be kept.
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $virtual_one
 * @property mixed $virtual_two
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 */
class Virtual extends Entity {

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

	protected function _getVirtualTwo() {
		// Missing return type and docblock means mixed as result
		return 'Virtual Two';
	}

	/**
	 * @return bool
	 */
	protected function _getVirtualReadOnly() {
		return true;
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
