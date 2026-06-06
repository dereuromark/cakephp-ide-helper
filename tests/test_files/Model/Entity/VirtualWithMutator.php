<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property array|null $params
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\Date $offer_date
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property string $slug
 *
 * @property-read string $label
 */
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
