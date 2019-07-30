<?php
namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask;
use IdeHelper\Console\Io;

class ClassAnnotatorTaskCollection {

	/**
	 * @var string[]
	 */
	protected $defaultTasks = [
		ModelAwareClassAnnotatorTask::class => ModelAwareClassAnnotatorTask::class,
		TestClassAnnotatorTask::class => TestClassAnnotatorTask::class,
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
	 * @param array $config
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
