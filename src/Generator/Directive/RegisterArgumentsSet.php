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
 * Then it can be used in other places as argumentsSet('mySet').
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#arguments-set
 */
class RegisterArgumentsSet extends BaseDirective {

	/**
	 * @var string
	 */
	public const NAME = 'registerArgumentsSet';

	/**
	 * @var string
	 */
	protected $set;

	/**
	 * @var array<string|\IdeHelper\ValueObject\ValueObjectInterface>
	 */
	protected $list;

	/**
	 * @param string $set
	 * @param array<string|\IdeHelper\ValueObject\ValueObjectInterface> $list
	 */
	public function __construct($set, array $list) {
		$this->set = $set;
		$this->list = $list;
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
	 * @return array<string, mixed>
	 */
	public function toArray() {
		return [
			'set' => $this->set,
			'list' => $this->list,
		];
	}

	/**
	 * @return string
	 */
	public function build() {
		$set = "'" . $this->set . "'";
		$list = $this->buildList($this->list);

		$result = <<<TXT
	registerArgumentsSet(
		$set,
$list
	);
TXT;

		return $result;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return 'argumentsSet(\'' . $this->set . '\')';
	}

}
