<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

class ConfigureTask implements TaskInterface {

	public const CLASS_TABLE = Configure::class;
	public const SET_CONFIGURE_KEYS = 'configureKeys';

	/**
	 * @var int[]
	 */
	protected $methods = [
		'\\' . self::CLASS_TABLE . '::read()' => 0,
		'\\' . self::CLASS_TABLE . '::readOrFail()' => 0,
		'\\' . self::CLASS_TABLE . '::check()' => 0,
		'\\' . self::CLASS_TABLE . '::write()' => 0,
		'\\' . self::CLASS_TABLE . '::delete()' => 0,
		'\\' . self::CLASS_TABLE . '::consume()' => 0,
		'\\' . self::CLASS_TABLE . '::consumeOrFail()' => 0,
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
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
	 * @return string[]
	 */
	protected function collectKeys(): array {
		$keys = [];

		$configure = (array)Configure::read();
		$keys = $this->addKeys($keys, $configure);

		ksort($keys);

		return $keys;
	}

	/**
	 * @param array $keys
	 * @param mixed $data
	 * @param array $path
	 *
	 * @return array
	 */
	protected function addKeys(array $keys, $data, array $path = []): array {
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
