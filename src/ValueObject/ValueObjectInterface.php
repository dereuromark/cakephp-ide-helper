<?php

namespace IdeHelper\ValueObject;

interface ValueObjectInterface {

	/**
	 * Creates itself from a string.
	 *
	 * @param string $value
	 *
	 * @return static
	 */
	public static function create(string $value);

	/**
	 * Returns raw input.
	 *
	 * @return string
	 */
	public function raw(): string;

	/**
	 * Returns formatted output.
	 *
	 * @return string
	 */
	public function __toString(): string;

}
