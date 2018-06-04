<?php
namespace IdeHelper\CodeCompletion;

use Cake\Core\Configure;
use IdeHelper\CodeCompletion\Task\BehaviorTask;
use IdeHelper\CodeCompletion\Task\TaskInterface;
use InvalidArgumentException;

class TaskCollection {

	/**
	 * @var array
	 */
	protected $defaultTasks = [
		BehaviorTask::class => BehaviorTask::class,
	];

	/**
	 * @var \IdeHelper\CodeCompletion\Task\TaskInterface[]
	 */
	protected $tasks;

	/**
	 * @param array $tasks
	 */
	public function __construct(array $tasks = []) {
		$defaultTasks = (array)Configure::read('IdeHelper.codeCompletionTasks') + $this->defaultTasks;
		$tasks += $defaultTasks;

		foreach ($tasks as $task) {
			if (!$task) {
				continue;
			}

			$this->add($task);
		}
	}

	/**
	 * Adds a task to the collection.
	 *
	 * @param string|\IdeHelper\CodeCompletion\Task\TaskInterface $task The task to map.
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function add($task) {
		if (is_string($task)) {
			$task = new $task();
		}

		$class = get_class($task);
		if (!$task instanceof TaskInterface) {
			throw new InvalidArgumentException(
				"Cannot use '$class' as task, it is not implementing " . TaskInterface::class . '.'
			);
		}

		$this->tasks[$class] = $task;

		return $this;
	}

	/**
	 * @return \IdeHelper\CodeCompletion\Task\TaskInterface[]
	 */
	public function tasks() {
		return $this->tasks;
	}

	/**
	 * @return array
	 */
	public function getMap() {
		$map = [];
		foreach ($this->tasks as $class => $task) {
			$snippet = $task->create();
			if (!$snippet) {
				continue;
			}

			$map[$task->type()][] = $snippet;
		}

		return $map;
	}

}
