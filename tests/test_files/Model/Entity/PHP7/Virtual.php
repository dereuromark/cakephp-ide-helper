<?php
namespace TestApp\Model\Entity\PHP7;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string $virtual_two
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 *
 * @property-read string|null $virtual_one {@uses \TestApp\Model\Entity\Virtual::_getVirtualOne()}
 */
class Virtual extends Entity {

	protected function _getVirtualOne(): ?string {
		return 'Virtual One';
	}

	protected function _getVirtualTwo(): string {
		return 'Virtual Two';
	}

}
