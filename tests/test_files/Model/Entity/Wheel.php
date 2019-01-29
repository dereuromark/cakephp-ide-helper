<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property mixed $virtual_one
 * @property mixed $virtual_two
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
	public function _getVirtualOne() {
		return 'Virtual One';
	}

	/**
	 * @return string|null
	 */
	public function _getVirtualTwo() {
		return 'Virtual Two';
	}

}
