<?php
namespace App\Model\Entity\PHP7;

use Cake\ORM\Entity;

class Virtual extends Entity {

	protected function _getVirtualOne(): ?string {
		return 'Virtual One';
	}

	protected function _getVirtualTwo(): string {
		return 'Virtual Two';
	}

}
