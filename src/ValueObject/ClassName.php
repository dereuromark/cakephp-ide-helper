<?php

namespace IdeHelper\ValueObject;

/**
 * Holds a FQCN string, which can be auto-casted to string
 */
class ClassName implements ValueObjectInterface {

	protected string $className;

	/**
	 * @param string $className
	 */
	private function __construct(string $className) {
		$this->className = $className;
	}

	/**
	 * Creates itself from a fully qualified class name.
	 *
	 * @param string $className
	 * @return static
	 */
	public static function create(string $className) {
		if (str_starts_with($className, '\\')) {
			$className = substr($className, 1);
		}

		return new static($className);
	}

	/**
	 * @return string
	 */
	public function raw(): string {
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return '\\' . $this->className . '::class';
	}

}
