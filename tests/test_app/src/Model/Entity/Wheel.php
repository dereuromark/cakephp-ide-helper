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
	public function _getVirtualOne() {
		return 'Virtual One';
	}

	/**
	 * Missing return type means mixed as result
	 */
	public function _getVirtualTwo() {
		return 'Virtual Two';
	}

}
