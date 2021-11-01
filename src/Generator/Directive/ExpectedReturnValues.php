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

	/**
	 * @var string
	 */
	public const NAME = 'expectedReturnValues';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var array<string|\IdeHelper\ValueObject\ValueObjectInterface>
	 */
	protected $list;

	/**
	 * @param string $method
	 * @param array<string|\IdeHelper\ValueObject\ValueObjectInterface> $list
	 */
	public function __construct($method, array $list) {
		$this->method = $method;
		$this->list = $list;
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
			'list' => $this->list,
		];
	}

	/**
	 * @return string
	 */
	public function build() {
		$method = $this->method;
		$list = $this->buildList($this->list);

		$result = <<<TXT
	expectedReturnValues(
		$method,
$list
	);
TXT;

		return $result;
	}

}
