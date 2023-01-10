<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\CallbackAnnotatorTask\TableCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTask\VirtualFieldCallbackAnnotatorTask;
use IdeHelper\Console\Io;

class CallbackAnnotatorTaskCollection {

	/**
	 * @var array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>, class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
	 */
	protected array $defaultTasks = [
		TableCallbackAnnotatorTask::class => TableCallbackAnnotatorTask::class,
		VirtualFieldCallbackAnnotatorTask::class => VirtualFieldCallbackAnnotatorTask::class,
	];

	/**
	 * @var array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
	 */
	protected array $tasks;

	/**
	 * @param array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>> $tasks
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
	 * @return array<class-string<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>>
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
	public function tasks(Io $io, array $config, string $path, string $content): array {
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
