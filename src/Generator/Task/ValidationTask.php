<?php

namespace IdeHelper\Generator\Task;

use Cake\Validation\Validator;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\ValueObject\StringName;

class ValidationTask extends ModelTask {

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$result = [];

		$result = $this->addValidatorRequirePresence($result);

		return $result;
	}

	/**
	 * @param \IdeHelper\Generator\Directive\BaseDirective[] $result
	 *
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	protected function addValidatorRequirePresence(array $result): array {
		$method = '\\' . Validator::class . '::requirePresence()';
		$list = [
			StringName::create('create'),
			StringName::create('update'),
		];
		$directive = new ExpectedArguments($method, 1, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

}
