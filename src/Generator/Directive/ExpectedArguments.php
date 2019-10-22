<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected method arguments.
 *
 * ### Example
 *
 * expectedArguments(
 *     \MyClass::addArgument(),
 *     1,
 *     \MyClass::OPTIONAL,
 *     \MyClass::REQUIRED
 * );
 *
 * or
 *
 * expectedArguments(
 *     \MyClass::getFlags(),
 *     0,
 *     argumentsSet("myFileObjectFlags")
 * );
 */
class ExpectedArguments extends BaseDirective {

	const NAME = 'expectedArguments';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var int
	 */
	protected $position;

	/**
	 * @var array
	 */
	protected $map;

	/**
	 * @param string $method
	 * @param int $position
	 * @param array $list
	 */
	public function __construct($method, $position, array $list) {
		$this->method = $method;
		$this->position = $position;
		$this->map = $list;
	}

	/**
	 * Key for sorting inside collection.
	 *
	 * @return string
	 */
	public function key() {
		return $this->method . '@' . $this->position . '@' . static::NAME;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return [
			'method' => $this->method,
			'position' => $this->position,
			'list' => $this->map,
		];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$method = $this->method;
		$position = $this->position;
		$list = $this->buildList($this->map);

		$result = <<<TXT
	expectedArguments(
		$method,
		$position,
$list
	);
TXT;

		return $result;
	}

}
