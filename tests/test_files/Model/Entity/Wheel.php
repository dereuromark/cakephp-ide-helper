<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 *
 * @property-read string|null $virtual_one
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

}
