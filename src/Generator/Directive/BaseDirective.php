<?php

namespace IdeHelper\Generator\Directive;

use IdeHelper\ValueObject\ValueObjectInterface;

/**
 * @see https://blog.jetbrains.com/phpstorm/2019/02/new-phpstorm-meta-php-features/
 * @method array toArray()
 */
abstract class BaseDirective {

	/**
	 * Key for sorting inside collection.
	 *
	 * @return string
	 */
	abstract public function key();

	/**
	 * Final PHP pseudo code.
	 *
	 * @return string
	 */
	abstract public function build();

	/**
	 * @param array $array
	 * @param int $indentation
	 *
	 * @return string
	 */
	protected function buildList(array $array, $indentation = 2): string {
		$result = [];
		foreach ($array as $alias => $value) {
			if ($value instanceof ValueObjectInterface) {
				$element = (string)$value;
			} elseif (is_array($value) && isset($value['escapeKey']) && $value['escapeKey'] === true) {
				$element = "'" . str_replace("'", "\'", $alias) . "'";
			} elseif (is_array($value)) {
				$element = $alias;
			} else {
				$element = $value;
			}
			$result[] = str_repeat("\t", $indentation) . $element;
		}

		return implode(',' . PHP_EOL, $result);
	}

	/**
	 * @param array $array
	 * @param int $indentation
	 *
	 * @return string
	 */
	protected function buildKeyValueMap(array $array, $indentation = 3): string {
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
