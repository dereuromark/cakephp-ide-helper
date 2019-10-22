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
 * Then it can be used in other places as argumentsSet("mySet").
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
	 * @param string $set
	 * @param array $list
	 */
	public function __construct($set, array $list) {
		$this->set = $set;
		$this->map = $list;
	}

	/**
	 * Key for sorting inside collection.
	 *
	 * @return string
	 */
	public function key() {
		return $this->set . '@' . static::NAME;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return [
			'set' => $this->set,
			'list' => $this->map,
		];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$set = "'" . $this->set . "'";
		$list = $this->buildList($this->map);

		$result = <<<TXT
	registerArgumentsSet(
		$set,
$list
	);
TXT;

		return $result;
	}

}
