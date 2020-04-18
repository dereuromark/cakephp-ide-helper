<?php

namespace IdeHelper\ValueObject;

/**
 * Holds a ValueObject key/value combination that will be treated literally on output.
 */
class KeyValue {

	/**
	 * @var \IdeHelper\ValueObject\ValueObjectInterface
	 */
	protected $key;

	/**
	 * @var \IdeHelper\ValueObject\ValueObjectInterface
	 */
	protected $value;

	/**
	 * @param \IdeHelper\ValueObject\ValueObjectInterface $key
	 * @param \IdeHelper\ValueObject\ValueObjectInterface $value
	 */
	private function __construct(ValueObjectInterface $key, ValueObjectInterface $value) {
		$this->key = $key;
		$this->value = $value;
	}

	/**
	 * Creates itself from a ValueObjectInterface key and value.
	 *
	 * @param \IdeHelper\ValueObject\ValueObjectInterface $key
	 * @param \IdeHelper\ValueObject\ValueObjectInterface $value
	 *
	 * @return static
	 */
	public static function create(ValueObjectInterface $key, ValueObjectInterface $value) {
		return new static($key, $value);
	}

	/**
	 * @return \IdeHelper\ValueObject\ValueObjectInterface
	 */
	public function key(): ValueObjectInterface {
		return $this->key;
	}

	/**
	 * @return \IdeHelper\ValueObject\ValueObjectInterface
	 */
	public function value(): ValueObjectInterface {
		return $this->value;
	}

}
