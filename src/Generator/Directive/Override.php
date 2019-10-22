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
 *         '' =>  '@|\Iterator',
 *     ])
 * );
 */
class Override extends BaseDirective {

	const NAME = 'override';

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
	 * @param array $map
	 */
	public function __construct($method, array $map) {
		$this->method = $method;
		$this->map = $map;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return [
			'method' => $this->method,
			'map' => $this->map,
		];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$method = $this->method;
		$mapDefinitions = $this->buildMapDefinitions($this->map);

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

	/**
	 * @param array $array
	 * @param int $indentation
	 *
	 * @return string
	 */
	protected function buildMapDefinitions(array $array, $indentation = 3) {
		$result = [];
		foreach ($array as $alias => $value) {
			if (is_array($value) && isset($value['escapeKey']) && $value['escapeKey'] === false) {
				$key = $alias;
			} else {
				$key = "'" . str_replace("'", "\'", $alias) . "'";
			}
			$result[] = str_repeat("\t", $indentation) . $key . ' => ' . $value . ',';
		}

		return implode(PHP_EOL, $result);
	}

}
