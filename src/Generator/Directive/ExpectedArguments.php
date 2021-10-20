<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected method arguments.
 *
 * The position is 0-based.
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
 *     argumentsSet('myFileObjectFlags')
 * );
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#expected-arguments
 */
class ExpectedArguments extends BaseDirective {

	/**
	 * @var string
	 */
	public const NAME = 'expectedArguments';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var int
	 */
	protected $position;

	/**
	 * @var array<string|\IdeHelper\ValueObject\ValueObjectInterface>
	 */
	protected $list;

	/**
	 * @param string $method
	 * @param int $position Position, 0-based.
	 * @param array<string|\IdeHelper\ValueObject\ValueObjectInterface> $list
	 */
	public function __construct($method, $position, array $list) {
		$this->method = $method;
		$this->position = $position;
		$this->list = $list;
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
	 * @return array<string, mixed>
	 */
	public function toArray() {
		return [
			'method' => $this->method,
			'position' => $this->position,
			'list' => $this->list,
		];
	}

	/**
	 * @return string
	 */
	public function build() {
		$method = $this->method;
		$position = $this->position;

		$list = $this->buildList($this->list);
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
