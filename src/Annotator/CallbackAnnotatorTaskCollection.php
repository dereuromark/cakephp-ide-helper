<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\CallbackAnnotatorTask\TableCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTask\VirtualFieldCallbackAnnotatorTask;
use IdeHelper\Console\Io;

class CallbackAnnotatorTaskCollection {

	/**
	 * @phpstan-var array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>, class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
	 *
	 * @var array<string, string>
	 */
	protected $defaultTasks = [
		TableCallbackAnnotatorTask::class => TableCallbackAnnotatorTask::class,
		VirtualFieldCallbackAnnotatorTask::class => VirtualFieldCallbackAnnotatorTask::class,
	];

	/**
	 * @phpstan-var array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
	 *
	 * @var array<string>
	 */
	protected $tasks;

	/**
	 * @phpstan-param array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>> $tasks
	 *
	 * @param array<string> $tasks
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
	 * @phpstan-return array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
	 *
	 * @return array<string>
	 */
	public function defaultTasks(): array {
		$tasks = (array)Configure::read('IdeHelper.callbackAnnotatorTasks') + $this->defaultTasks;

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
	 * @param string $path
	 * @param string $content
	 * @return array<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>
	 */
	public function tasks(Io $io, array $config, $path, $content) {
		$tasks = $this->tasks;

		$collection = [];
		foreach ($tasks as $task) {
			/** @var \IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface $object */
			$object = new $task($io, $config, $path, $content);
			$collection[] = $object;
		}

		return $collection;
	}

}
