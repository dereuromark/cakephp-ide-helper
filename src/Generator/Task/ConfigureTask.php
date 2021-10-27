<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

class ConfigureTask implements TaskInterface {

	public const CLASS_CONFIGURE = Configure::class;

	/**
	 * @var string
	 */
	public const SET_CONFIGURE_KEYS = 'configureKeys';

	/**
	 * @var array<int>
	 */
	protected $methods = [
		'\\' . self::CLASS_CONFIGURE . '::read()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::readOrFail()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::check()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::write()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::delete()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::consume()' => 0,
		'\\' . self::CLASS_CONFIGURE . '::consumeOrFail()' => 0,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectKeys();
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_CONFIGURE_KEYS, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach ($this->methods as $method => $position) {
			$directive = new ExpectedArguments($method, $position, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectKeys(): array {
		$keys = [];

		$configure = (array)Configure::read();
		$keys = $this->addKeys($keys, $configure);

		ksort($keys);

		return $keys;
	}

	/**
	 * @param array<string, mixed> $keys
	 * @param array<mixed> $data
	 * @param array<string> $path
	 *
	 * @return array<string, mixed>
	 */
	protected function addKeys(array $keys, array $data, array $path = []): array {
		foreach ($data as $key => $row) {
			if (is_numeric($key)) {
				continue;
			}

			$subPath = $path;
			$subPath[] = $key;
			$subPathString = implode('.', $subPath);

			$keys[$subPathString] = StringName::create($subPathString);

			if (!is_array($row)) {
				continue;
			}

			$keys = $this->addKeys($keys, $row, $subPath);
		}

		return $keys;
	}

}
