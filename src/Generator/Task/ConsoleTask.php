<?php

namespace IdeHelper\Generator\Task;

use Cake\Console\ConsoleIo;
use IdeHelper\Generator\Directive\ExitPoint;

class ConsoleTask implements TaskInterface {

	const METHOD_ABORT = '\\' . ConsoleIo::class . '::abort()';

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect() {
		$result = [];

		$directive = new ExitPoint(static::METHOD_ABORT);
		$result[$directive->key()] = $directive;

		return $result;
	}

}
