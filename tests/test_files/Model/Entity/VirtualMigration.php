<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property-read string $read_only_field
 * @property string $writable_field
 * @property array|null $params
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\Date $offer_date
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class VirtualMigration extends Entity {

	/**
	 * Read-only: no mutator, so the stale `@property` flips to `@property-read`.
	 *
	 * @return string
	 */
	protected function _getReadOnlyField(): string {
		return 'ro';
	}

	/**
	 * Writable: has a mutator, so the stale `@property-read` flips to `@property`.
	 *
	 * @return string
	 */
	protected function _getWritableField(): string {
		return 'rw';
	}

	/**
	 * @param string $value
	 * @return string
	 */
	protected function _setWritableField(string $value): string {
		return $value;
	}

}
