<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property string $read_only_field
 * @property-read string $writable_field
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
