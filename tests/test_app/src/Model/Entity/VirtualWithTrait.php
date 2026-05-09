<?php
namespace TestApp\Model\Entity;

use Cake\ORM\Entity;
use TestApp\Model\Entity\Traits\DatePrecisionEntityTrait;

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
