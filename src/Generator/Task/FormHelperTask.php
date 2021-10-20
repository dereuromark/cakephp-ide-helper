<?php

namespace IdeHelper\Generator\Task;

use Cake\ORM\TableRegistry;
use Cake\View\Helper\FormHelper;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\App;
use IdeHelper\ValueObject\StringName;
use ReflectionClass;
use Throwable;

class FormHelperTask extends ModelTask {

	public const CLASS_FORM_HELPER = FormHelper::class;

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectFieldNames();

		ksort($list);

		$method = '\\' . static::CLASS_FORM_HELPER . '::control()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectFieldNames(): array {
		$models = $this->collectModels();

		$allFields = [];
		foreach ($models as $model => $className) {
			/** @phpstan-var class-string<object>|null $tableClass */
			$tableClass = App::className($model, 'Model/Table', 'Table');
			if (!$tableClass) {
				continue;
			}

			$tableReflection = new ReflectionClass($tableClass);
			if (!$tableReflection->isInstantiable()) {
				continue;
			}

			try {
				$modelObject = TableRegistry::getTableLocator()->get($model);
				$fields = $modelObject->getSchema()->columns();

			} catch (Throwable $exception) {
				continue;
			}

			$allFields = array_merge($allFields, $fields);
		}

		$allFields = array_unique($allFields);

		$list = [];
		foreach ($allFields as $field) {
			$list[$field] = StringName::create($field);
		}

		return $list;
	}

}
