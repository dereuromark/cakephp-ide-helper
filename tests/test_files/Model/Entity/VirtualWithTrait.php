<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;
use TestApp\Model\Entity\Traits\DatePrecisionEntityTrait;

/**
 * @property array|null $params
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\Date $offer_date
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property-read string $in_class_virtual
 * @property-read bool $is_date_approximate
 * @property-read string|null $date_precision_string
 * @property-read array<int, string> $date_precision_labels
 */
class VirtualWithTrait extends Entity {

	use DatePrecisionEntityTrait;

	protected array $_virtual = [
		'is_date_approximate',
		'date_precision_string',
		'date_precision_labels',
		'in_class_virtual',
	];

	/**
	 * @return string
	 */
	protected function _getInClassVirtual(): string {
		return 'in-class';
	}

}
