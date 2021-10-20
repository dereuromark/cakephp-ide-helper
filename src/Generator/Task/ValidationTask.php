<?php

namespace IdeHelper\Generator\Task;

use Cake\Validation\Validator;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

class ValidationTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const SET_VALIDATION_WHEN = 'validationWhen';

	/**
	 * @phpstan-var array<string, int>
	 *
	 * @var array<int>
	 */
	protected static $methods = [
		'requirePresence' => 1,
		'allowEmptyFor' => 2,
		'allowEmptyString' => 2,
		'allowEmptyFile' => 2,
		'allowEmptyArray' => 2,
		'allowEmptyDate' => 2,
		'allowEmptyTime' => 2,
		'allowEmptyDateTime' => 2,
		'notEmptyString' => 2,
		'notEmptyFile' => 2,
		'notEmptyArray' => 2,
		'notEmptyDate' => 2,
		'notEmptyTime' => 2,
		'notEmptyDateTime' => 2,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->getValidatorRequirePresence();
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_VALIDATION_WHEN, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach (static::$methods as $method => $position) {
			$method = '\\' . Validator::class . '::' . $method . '()';
			$directive = new ExpectedArguments($method, $position, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<\IdeHelper\ValueObject\ValueObjectInterface>
	 */
	protected function getValidatorRequirePresence(): array {
		return [
			StringName::create('create'),
			StringName::create('update'),
		];
	}

}
