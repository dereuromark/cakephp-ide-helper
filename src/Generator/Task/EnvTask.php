<?php

namespace IdeHelper\Generator\Task;

use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\ValueObject\StringName;

class EnvTask implements TaskInterface {

	/**
	 * @var string
	 */
	protected const METHOD_ENV = '\\' . 'env()';

	/**
	 * Keys from Web based request, will be merged with CLI ones.
	 *
	 * @var array<string>
	 */
	protected static $keys = [
		'HTTP_HOST',
		'HTTPS',
		'REMOTE_ADDR',
		'REMOTE_PORT',
		'DOCUMENT_ROOT',
		'DOCUMENT_URI',
		'PHP_SELF',
		'CGI_MODE',
		'SCRIPT_NAME',
		'HTTP_ACCEPT_LANGUAGE',
		'HTTP_ACCEPT_ENCODING',
		'HTTP_ACCEPT',
		'HTTP_COOKIE',
		'HTTP_USER_AGENT',
		'HTTP_CONNECTION',
		'HOME',
		'REDIRECT_STATUS',
		'SERVER_NAME',
		'SERVER_PORT',
		'SERVER_PROTOCOL',
		'GATEWAY_INTERFACE',
		'REQUEST_SCHEME',
		'REQUEST_URI',
		'REQUEST_METHOD',
		'CONTENT_LENGTH',
		'CONTENT_TYPE',
		'QUERY_STRING',
		'REQUEST_TIME',
		'SCRIPT_FILENAME',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$keys = $this->envKeys();

		ksort($keys);

		$method = static::METHOD_ENV;
		$directive = new ExpectedArguments($method, 0, $keys);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<\IdeHelper\ValueObject\StringName>
	 */
	protected function envKeys(): array {
		$keys = array_keys($_SERVER);
		$keys = array_merge($keys, static::$keys);
		$keys = array_unique($keys);

		$list = [];

		foreach ($keys as $key) {
			if (strpos($key, '_') === 0) {
				continue;
			}

			$list[$key] = StringName::create($key);
		}

		return $list;
	}

}
