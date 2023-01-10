<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\ClassAnnotatorTask\FormClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\MailerClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask;
use IdeHelper\Console\Io;

class ClassAnnotatorTaskCollection {

	/**
	 * @var array<class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>, class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>>
	 */
	protected array $defaultTasks = [
		ModelAwareClassAnnotatorTask::class => ModelAwareClassAnnotatorTask::class,
		FormClassAnnotatorTask::class => FormClassAnnotatorTask::class,
		MailerClassAnnotatorTask::class => MailerClassAnnotatorTask::class,
		TestClassAnnotatorTask::class => TestClassAnnotatorTask::class,
	];

	/**
	 * @var array<class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>>
	 */
	protected array $tasks;

	/**
	 * @param array<class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>> $tasks
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
	 * @return array<class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>>
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
	 * @return array<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>
	 */
	public function tasks(Io $io, array $config, string $content): array {
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
