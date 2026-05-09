<?php
namespace TestApp\Model\Entity\Traits;

trait DatePrecisionEntityTrait {

	/**
	 * @return bool
	 */
	protected function _getIsDateApproximate(): bool {
		return $this->get('date_precision') > 1;
	}

	/**
	 * @return string|null
	 */
	protected function _getDatePrecisionString(): ?string {
		return match ($this->get('date_precision')) {
			1 => 'exact',
			2 => 'week',
			3 => 'month',
			4 => 'quarter',
			default => null,
		};
	}

}
