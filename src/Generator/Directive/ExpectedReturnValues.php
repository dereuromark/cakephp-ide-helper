<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected method return values.
 *
 * ### Example
 *
 * expectedReturnValues(
 *     \MyClass::addArgument(),
 *     1,
 *     \MyClass::OPTIONAL,
 *     \MyClass::REQUIRED
 * );
 */
class ExpectedReturnValues extends BaseDirective {

	const NAME = 'expectedReturnValues';

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
