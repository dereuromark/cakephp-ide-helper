<?php
namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareClassAnnotatorTask;
use IdeHelper\Console\Io;

class ClassAnnotatorTaskCollection {

	/**
	 * @var string[]
	 */
	protected $defaultTasks = [
		ModelAwareClassAnnotatorTask::class => ModelAwareClassAnnotatorTask::class,
	];

	/**
	 * @var string[]
	 */
	protected $tasks;

	/**
	 * @param array $tasks
	 */
	public function __construct(array $tasks = []) {
		$defaultTasks = (array)Configure::read('IdeHelper.classAnnotatorTasks') + $this->defaultTasks;
		$tasks += $defaultTasks;

		foreach ($tasks as $task) {
			if (!$task) {
				continue;
			}

			$this->tasks = $tasks;
		}
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
