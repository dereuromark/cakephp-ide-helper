<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected method return values.
 *
 * ### Example
 *
 * expectedReturnValues(
 *     \MyClass::addArgument(),
 *     \MyClass::SUCCESS,
 *     \MyClass::ERROR
 * );
 *
 * or
 *
 * expectedReturnValues(
 *     \MyClass::getFlags(),
 *     argumentsSet('myFileObjectFlags')
 * );
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#expected-return-values
 */
class ExpectedReturnValues extends BaseDirective {

	public const NAME = 'expectedReturnValues';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var array
	 */
	protected $map;

	/**
	 * @param string $method
	 * @param array $list
	 */
	public function __construct($method, array $list) {
		$this->method = $method;
		$this->map = $list;
	}

	/**
	 * Key for sorting inside collection.
	 *
	 * @return string
	 */
	public function key() {
		return $this->method . '@' . static::NAME;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray() {
		return [
			'method' => $this->method,
			'list' => $this->map,
		];
	}

	/**
	 * @return string
	 */
	public function build() {
		$method = $this->method;
		$list = $this->buildList($this->map);

		$result = <<<TXT
	expectedReturnValues(
		$method,
$list
	);
TXT;

		return $result;
	}

}
