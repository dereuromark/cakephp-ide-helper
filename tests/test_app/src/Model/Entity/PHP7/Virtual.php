<?php
namespace TestApp\Model\Entity\PHP7;

use Cake\ORM\Entity;

class Virtual extends Entity {

	protected function _getVirtualOne(): ?string {
		return 'Virtual One';
	}

	protected function _getVirtualTwo(): string {
		return 'Virtual Two';
	}

	/**
	 * @return array<int, array<string>>
	 */
	protected function _getNestedGeneric(): array {
		return [[1 => ['a', 'b']]];
	}

}
