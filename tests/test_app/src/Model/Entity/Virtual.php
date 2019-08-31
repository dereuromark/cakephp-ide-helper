<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property-read bool $virtual_read_only This should be kept.
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
	 * @param \App\Model\Entity\Wheel[] $wheels
	 *
	 * @return \App\Model\Entity\Wheel[]
	 */
	protected function _getWheels($wheels = []) {
		return $wheels;
	}

}
