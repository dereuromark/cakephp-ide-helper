<?php

namespace IdeHelper\ValueObject;

/**
 * Holds a string that will be treated literally on output (no extra quoting).
 */
class LiteralName implements ValueObjectInterface {

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @param string $value
	 */
	private function __construct(string $value) {
		$this->value = $value;
	}

	/**
	 * Creates itself from a string.
	 *
	 * @param string $value
	 *
	 * @return static
	 */
	public static function create(string $value) {
		return new static($value);
	}

	/**
	 * @return string
	 */
	public function raw(): string {
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return $this->raw();
	}

}
