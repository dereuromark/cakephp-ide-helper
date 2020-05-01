<?php

namespace IdeHelper\ValueObject;

/**
 * Holds a FQCN string, which can be auto-casted to string
 */
class ClassName implements ValueObjectInterface {

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @param string $className
	 */
	private function __construct($className) {
		$this->className = $className;
	}

	/**
	 * Creates itself from a fully qualified class name.
	 *
	 * @param string $className
	 * @return static
	 */
	public static function create($className) {
		if (strpos($className, '\\') === 0) {
			$className = substr($className, 1);
		}

		return new static($className);
	}

	/**
	 * @return string
	 */
	public function raw() {
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return '\\' . $this->className . '::class';
	}

}
