<?php

namespace IdeHelper\Generator\Task;

use Cake\Console\ConsoleIo;
use IdeHelper\Generator\Directive\ExitPoint;

class ConsoleTask implements TaskInterface {

	/**
	 * @var string
	 */
	protected const METHOD_ABORT = '\\' . ConsoleIo::class . '::abort()';

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$directive = new ExitPoint(static::METHOD_ABORT);
		$result[$directive->key()] = $directive;

		return $result;
	}

}
