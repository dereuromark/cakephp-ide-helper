<?php

namespace IdeHelper\Generator\Task;

interface TaskInterface {

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array;

}
