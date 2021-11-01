<?php

namespace IdeHelper\Generator\Task;

use Cake\Http\ServerRequest;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\ValueObject\StringName;

class RequestTask implements TaskInterface {

	public const CLASS_REQUEST = ServerRequest::class;

	/**
	 * @var array<string>
	 */
	protected static $paramKeys = [
		'controller',
		'action',
		'plugin',
		'prefix',
		'pass',
		'_matchedRoute',
		'_ext',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectParamKeys();

		$method = '\\' . static::CLASS_REQUEST . '::getParam()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectParamKeys(): array {
		$keys = [];
		foreach (static::$paramKeys as $key) {
			$keys[$key] = StringName::create($key);
		}

		ksort($keys);

		return $keys;
	}

}
