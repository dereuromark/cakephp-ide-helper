<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

class Wheel extends Entity {

	protected array $virtual = [
		'virtual_one',
	];

	/**
	 * @return string|null
	 */
	protected function _getVirtualOne() {
		return 'Virtual One';
	}

}
