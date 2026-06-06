<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

class VirtualWithMutator extends Entity {

	/**
	 * Computed value without a mutator: pure read-only virtual field.
	 *
	 * @return string
	 */
	protected function _getLabel(): string {
		return 'label';
	}

	/**
	 * Computed value paired with a mutator below, so it stays writable.
	 *
	 * @return string
	 */
	protected function _getSlug(): string {
		return 'slug';
	}

	/**
	 * @param string $slug
	 * @return string
	 */
	protected function _setSlug(string $slug): string {
		return $slug;
	}

}
