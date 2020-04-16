<?php

namespace TestApp\Generator\Task;

use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Task\EnvTask;
use IdeHelper\ValueObject\StringName;

class TestEnvTask extends EnvTask {

	/**
	 * @return \IdeHelper\ValueObject\StringName[]
	 */
	protected function envKeys(): array {
		$list = parent::envKeys();

		$list = [
			'HTTP_HOST' => $list['HTTP_HOST'],
		];

		return $list;
	}

}
