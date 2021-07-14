<?php

namespace IdeHelper\ValueObject;

/**
 * Helps to use an existing argument set to be used in other directives for DRY code.
 *
 * Then it can be used in other places as argumentsSet('mySet').
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#arguments-set
 */
class ArgumentsSet implements ValueObjectInterface {

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
		return 'argumentsSet(\'' . $this->value . '\')';
	}

}
