<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to register an argument set to be used in other directives for DRY code.
 *
 * ### Example
 *
 * registerArgumentsSet(
 *     'mySet',
 *     \MyClass::OPTIONAL,
 *     \MyClass::REQUIRED
 * );
 *
 * Then it can be used in other places as
 */
class RegisterArgumentsSet extends BaseDirective {

	const NAME = 'registerArgumentsSet';

	/**
	 * @var string
	 */
	protected $set;

	/**
	 * @var array
	 */
	protected $map;

	/**
	 * Key for sorting inside collection.
	 *
	 * @return string
	 */
	public function key() {
		return $this->set . '@' . static::NAME;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return 'TODO';
	}

}
