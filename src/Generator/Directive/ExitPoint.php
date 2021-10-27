<?php

namespace IdeHelper\Generator\Directive;

/**
 * Helps to annotate expected exit point methods.
 *
 * This is available since PhpStorm 2020.01
 *
 * ### Example
 *
 * exitPoint(\Cake\Console\ConsoleIo::abort());
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html#define-exit-points
 */
class ExitPoint extends BaseDirective {

	/**
	 * @var string
	 */
	public const NAME = 'exitPoint';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @param string $method
	 */
	public function __construct($method) {
		$this->method = $method;
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
	 * @return array<string, mixed>
	 */
	public function toArray() {
		return [
			'method' => $this->method,
		];
	}

	/**
	 * @return string
	 */
	public function build() {
		$method = $this->method;

		$result = <<<TXT
	exitPoint($method);
TXT;

		return $result;
	}

}
