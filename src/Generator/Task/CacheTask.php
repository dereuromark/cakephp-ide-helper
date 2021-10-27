<?php

namespace IdeHelper\Generator\Task;

use Cake\Cache\Cache;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

class CacheTask implements TaskInterface {

	public const CLASS_CACHE = Cache::class;

	/**
	 * @var string
	 */
	public const SET_CACHE_ENGINES = 'cacheEngines';

	/**
	 * @var array<int>
	 */
	protected $aliases = [
		'\\' . self::CLASS_CACHE . '::clear()' => 0,
		'\\' . self::CLASS_CACHE . '::read()' => 1,
		'\\' . self::CLASS_CACHE . '::readMany()' => 1,
		'\\' . self::CLASS_CACHE . '::delete()' => 1,
		'\\' . self::CLASS_CACHE . '::deleteMany()' => 1,
		'\\' . self::CLASS_CACHE . '::clearGroup()' => 1,
		'\\' . self::CLASS_CACHE . '::add()' => 2,
		'\\' . self::CLASS_CACHE . '::write()' => 2,
		'\\' . self::CLASS_CACHE . '::increment()' => 2,
		'\\' . self::CLASS_CACHE . '::decrement()' => 2,
		'\\' . self::CLASS_CACHE . '::remember()' => 2,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectCacheEngines();
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_CACHE_ENGINES, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach ($this->aliases as $alias => $position) {
			$directive = new ExpectedArguments($alias, $position, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<\IdeHelper\ValueObject\StringName>
	 */
	protected function collectCacheEngines(): array {
		$cacheEngines = Cache::configured();

		$result = [];
		foreach ($cacheEngines as $cacheEngine) {
			$result[$cacheEngine] = StringName::create($cacheEngine);
		}

		ksort($result);

		return $result;
	}

}
