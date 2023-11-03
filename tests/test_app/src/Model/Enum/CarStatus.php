<?php
declare(strict_types=1);

namespace TestApp\Model\Enum;

enum CarStatus: int
{
	case NEW = 0;
	case USED = 1;

	public function label(): string {
		return match($this) {
			self::NEW => 'new',
			self::USED => 'used',
		};
	}
}
