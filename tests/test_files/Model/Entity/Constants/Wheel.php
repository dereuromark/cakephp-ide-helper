<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property array{foo: string}|null $params
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

	const FIELD_PARAMS = 'params';
	const FIELD_ID = 'id';
	const FIELD_NAME = 'name';
	const FIELD_CONTENT = 'content';
	const FIELD_OFFER_DATE = 'offer_date';
	const FIELD_CREATED = 'created';
	const FIELD_MODIFIED = 'modified';
	const FIELD_WHEELS = 'wheels';
	const FIELD_VIRTUAL_ONE = 'virtual_one';

}
