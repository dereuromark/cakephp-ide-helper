<?php

namespace IdeHelper\Generator\Task;

use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\ValueObject\StringName;

class ConnectionTask implements TaskInterface {

	protected const METHOD_GET = '\\' . ConnectionManager::class . '::get()';

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$result = [];

		$keys = $this->connectionKeys();

		ksort($keys);

		$directive = new ExpectedArguments(static::METHOD_GET, 0, $keys);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return \IdeHelper\ValueObject\StringName[]
	 */
	protected function connectionKeys(): array {
		$configured = ConnectionManager::configured();

		$list = [];
		foreach ($configured as $key) {
			$list[$key] = StringName::create($key);
		}

		return $list;
	}

}
