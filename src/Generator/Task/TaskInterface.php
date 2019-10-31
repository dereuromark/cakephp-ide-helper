<?php
namespace IdeHelper\Generator\Task;

interface TaskInterface {

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array;

}
