<?php
namespace App\Model\Entity\PHP7;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $virtual_one
 * @property string $virtual_two
 * @property \App\Model\Entity\Wheel[] $wheels
 */
class Virtual extends Entity {

	protected function _getVirtualOne(): ?string {
		return 'Virtual One';
	}

	protected function _getVirtualTwo(): string {
		return 'Virtual Two';
	}

}
