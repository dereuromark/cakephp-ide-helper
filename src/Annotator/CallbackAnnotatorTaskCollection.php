<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\CallbackAnnotatorTask\TableCallbackAnnotatorTask;
use IdeHelper\Console\Io;

class CallbackAnnotatorTaskCollection {

	/**
	 * @var string[]
	 */
	protected $defaultTasks = [
		TableCallbackAnnotatorTask::class => TableCallbackAnnotatorTask::class,
	];

	/**
	 * @var string[]
	 */
	protected $tasks;

	/**
	 * @param array $tasks
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
	 * @return string[]
	 */
	public function defaultTasks() {
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
	 * @param array $config
	 * @param string $path
	 * @param string $content
	 * @return \IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface[]
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
