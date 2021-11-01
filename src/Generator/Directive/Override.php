<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected method argument and return type combinations.
 *
 * ### Example
 *
 * override(
 *     \MyClass::addArgument(0),
 *     map([,
 *         'A' => \MyClass::class,
 *         '' => '@|\Iterator',
 *     ])
 * );
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#override
 */
class Override extends BaseDirective {

	/**
	 * @var string
	 */
	public const NAME = 'override';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var array<string, string|\IdeHelper\ValueObject\ValueObjectInterface>
	 */
	protected $map;

	/**
	 * @param string $method
	 * @param array<string, string|\IdeHelper\ValueObject\ValueObjectInterface> $map
	 */
	public function __construct($method, array $map) {
		$this->method = $method;
		$this->map = $map;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray() {
		return [
			'method' => $this->method,
			'map' => $this->map,
		];
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
	 * @return string
	 */
	public function build() {
		$method = $this->method;
		$mapDefinitions = $this->buildKeyValueMap($this->map);

		$result = <<<TXT
	override(
		$method,
		map([
$mapDefinitions
		])
	);
TXT;

		return $result;
	}

}
