<?php
namespace IdeHelper\Generator;

use Cake\Core\Configure;
use IdeHelper\Generator\Task\ModelTask;
use IdeHelper\Generator\Task\TaskInterface;
use InvalidArgumentException;

class TaskCollection {

	/**
	 * @var array
	 */
	protected $defaultTasks = [
		ModelTask::class,
	];

	/**
	 * @var array
	 */
	protected $tasks;

	/**
	 * @param array $tasks
	 */
	public function __construct(array $tasks = []) {
		if (!$tasks) {
			$configTasks = (array)Configure::read('IdeHelper.tasks');
			$tasks = array_merge($this->defaultTasks, $configTasks);
		}

		foreach ($tasks as $task) {
			$this->add($task);
		}
	}

	/**
	 * Adds a task to the collection.
	 *
	 * @param string|\IdeHelper\Generator\Task\TaskInterface $task The task to map.
	 * @return $this
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
	 * @return \IdeHelper\Generator\Task\TaskInterface[]
	 */
	public function tasks() {
		return $this->tasks;
	}

}
