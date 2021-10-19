<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask;
use IdeHelper\Console\Io;

class ClassAnnotatorTaskCollection {

	/**
	 * @phpstan-var class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>[]
	 *
	 * @var string[]
	 */
	protected $defaultTasks = [
		ModelAwareClassAnnotatorTask::class => ModelAwareClassAnnotatorTask::class,
		TestClassAnnotatorTask::class => TestClassAnnotatorTask::class,
	];

	/**
	 * @phpstan-var class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>[]
	 *
	 * @var string[]
	 */
	protected $tasks;

	/**
	 * @phpstan-param class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>[] $tasks
	 *
	 * @param string[] $tasks
	 */
	public function __construct(array $tasks = []) {
		$defaultTasks = $this->defaultTasks();
		$tasks += $defaultTasks;

		foreach ($tasks as $task) {
			if (!$task) {
				continue;
			}

			$this->tasks = $tasks;
		}
	}

	/**
	 * @phpstan-return class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>[]
	 *
	 * @return string[]
	 */
	public function defaultTasks(): array {
		$tasks = (array)Configure::read('IdeHelper.classAnnotatorTasks') + $this->defaultTasks;

		foreach ($tasks as $k => $v) {
			if (is_numeric($k)) {
				$tasks[$v] = $v;
				unset($tasks[$k]);
			}
		}

		return $tasks;
	}

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 * @param string $content
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface[]
	 */
	public function tasks(Io $io, array $config, $content) {
		$tasks = $this->tasks;

		$collection = [];
		foreach ($tasks as $task) {
			/** @var \IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface $object */
			$object = new $task($io, $config, $content);
			$collection[] = $object;
		}

		return $collection;
	}

}
