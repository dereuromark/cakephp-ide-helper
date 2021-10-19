<?php

namespace IdeHelper\Generator\Directive;

use IdeHelper\ValueObject\KeyValue;
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
	 * @param array<string|\IdeHelper\ValueObject\ValueObjectInterface> $array
	 * @param int $indentation
	 *
	 * @return string
	 */
	protected function buildList(array $array, $indentation = 2): string {
		$result = [];
		foreach ($array as $value) {
			if ($value instanceof ValueObjectInterface) {
				$element = (string)$value;
			} else {
				$element = $value;
			}
			$result[] = str_repeat("\t", $indentation) . $element;
		}

		return implode(',' . PHP_EOL, $result);
	}

	/**
	 * @param array<string, string|\IdeHelper\ValueObject\ValueObjectInterface> $array
	 * @param int $indentation
	 *
	 * @return string
	 */
	protected function buildKeyValueMap(array $array, $indentation = 3): string {
		$result = [];
		foreach ($array as $alias => $value) {
			if ($value instanceof KeyValue) {
				$key = $value->key();
				$value = $value->value();
			} else {
				$key = "'" . str_replace("'", "\'", $alias) . "'";
			}
			$result[] = str_repeat("\t", $indentation) . $key . ' => ' . $value . ',';
		}

		return implode(PHP_EOL, $result);
	}

}
