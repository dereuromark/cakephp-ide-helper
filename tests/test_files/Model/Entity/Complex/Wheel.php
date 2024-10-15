<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property array{user?: int, account?: int|string, newContacts?: array<mixed>}|null $params !
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\Date $offer_date
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 *
 * @property-read string|null $virtual_one
 */
class Wheel extends Entity {

	protected array $_virtual = [
		'virtual_one',
	];

	/**
	 * @return string|null
	 */
	protected function _getVirtualOne() {
		return 'Virtual One';
	}

}
