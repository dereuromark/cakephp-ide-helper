<?php

namespace IdeHelper\Annotator\CallbackAnnotatorTask;

use Cake\ORM\TableRegistry;
use IdeHelper\Annotation\ParamAnnotation;
use PHP_CodeSniffer\Files\File;
use Throwable;

/**
 * Fix up generic *(EventInterface $event, EntityInterface $entity, ...) hook methods to have at least the $entity documented as concrete class.
 */
class TableCallbackAnnotatorTask extends AbstractCallbackAnnotatorTask implements CallbackAnnotatorTaskInterface {

	/**
	 * @var array<string, string>
	 */
	protected $callbacks = [
		'beforeRules' => 'beforeRules',
		'afterRules' => 'afterRules',
		'beforeSave' => 'beforeSave',
		'afterSave' => 'afterSave',
		'afterSaveCommit' => 'afterSaveCommit',
		'beforeDelete' => 'beforeDelete',
		'afterDelete' => 'afterDelete',
		'afterDeleteCommit' => 'afterDeleteCommit',
	];

	/**
	 * @var string|null
	 */
	protected $entityClassName;

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Table' || substr($className, -5) !== 'Table') {
			return false;
		}

		$modelName = substr($className, 0, -5);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);

		try {
			$table = TableRegistry::getTableLocator()->get($plugin ? ($plugin . '.' . $modelName) : $modelName);
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table: ' . $e->getMessage());
			}

			return false;
		}

		$entityClassName = $table->getEntityClass();
		$this->entityClassName = $entityClassName;

		if (!preg_match('#\bfunction (' . $this->generatePattern() . ')\b#', $this->content)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$file = $this->getFile($path, $this->content);

		$methods = $this->getMethods($file);

		foreach ($methods as $index => $method) {
			if (!in_array($method['name'], $this->callbacks, true)) {
				unset($methods[$index]);

				continue;
			}

			if (!$this->needsUpdate($file, $index, $method)) {
				unset($methods[$index]);

				continue;
			}

			$methods[$index] = $method;
		}

		return $this->annotateMethods($path, $file, $methods);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @param array<string, mixed> $method
	 * @return bool
	 */
	protected function needsUpdate(File $file, int $index, array &$method): bool {
		if (empty($method['docBlockStart']) || empty($method['docBlockEnd'])) {
			return false;
		}

		$annotations = $this->parseExistingAnnotations($file, $method['docBlockEnd'], ['@param']);

		if (empty($annotations[1])) {
			return false;
		}

		/** @var \IdeHelper\Annotation\ParamAnnotation $currentAnnotation */
		$currentAnnotation = $annotations[1];
		$expectedAnnotation = new ParamAnnotation('\\' . $this->entityClassName, $currentAnnotation->getVariable());

		if ($currentAnnotation->getType() === $expectedAnnotation->getType()) {
			return false;
		}

		$currentAnnotation->replaceWith($expectedAnnotation);

		$method['annotations'] = [
			$currentAnnotation,
		];

		return true;
	}

	/**
	 * @return string
	 */
	protected function generatePattern(): string {
		$pattern = [];
		foreach ($this->callbacks as $key => $v) {
			$pattern[] = preg_quote($key . '(');
		}

		return implode('|', $pattern);
	}

}
