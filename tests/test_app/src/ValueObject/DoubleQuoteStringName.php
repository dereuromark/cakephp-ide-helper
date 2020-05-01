<?php

namespace TestApp\ValueObject;

use IdeHelper\ValueObject\StringName;

/**
 * Let's use " instead of ' here.
 */
class DoubleQuoteStringName extends StringName {

	/**
	 * @return string
	 */
	public function __toString() {
		return '"' . $this->value . '"';
	}

}
