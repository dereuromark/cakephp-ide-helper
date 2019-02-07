<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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

	protected function _getVirtualTwo() {
		// Missing return type and docblock means mixed as result
		return 'Virtual Two';
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
