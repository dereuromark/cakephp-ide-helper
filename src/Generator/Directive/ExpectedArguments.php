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
 */
class ExpectedArguments extends BaseDirective {

	const NAME = 'expectededArguments';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var array
	 */
	protected $map;

	/**
	 * @return string
	 */
	public function __toString() {
		return 'TODO';
	}

}
