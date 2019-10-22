<?php

namespace IdeHelper\Generator\Directive;

/**
 * @see https://blog.jetbrains.com/phpstorm/2019/02/new-phpstorm-meta-php-features/
 * @property string $method
 */
abstract class BaseDirective {

	/**
	 * Define a name per directive.
	 */
	const NAME = '';

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
	abstract public function __toString();

}
