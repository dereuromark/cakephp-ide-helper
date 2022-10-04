<?php

namespace IdeHelper\CodeCompletion;

use Cake\Core\Configure;
use IdeHelper\CodeCompletion\Task\BehaviorTask;
use IdeHelper\CodeCompletion\Task\ControllerEventsTask;
use IdeHelper\CodeCompletion\Task\ModelEventsTask;
use IdeHelper\CodeCompletion\Task\TaskInterface;
use IdeHelper\CodeCompletion\Task\ViewEventsTask;
use InvalidArgumentException;

class TaskCollection {

	/**
	 * @phpstan-var array<class-string<\IdeHelper\CodeCompletion\Task\TaskInterface>, class-string<\IdeHelper\CodeCompletion\Task\TaskInterface>>
	 *
	 * @var array<string, string>
	 */
	protected $defaultTasks = [
		BehaviorTask::class => BehaviorTask::class,
		ModelEventsTask::class => ModelEventsTask::class,
		ControllerEventsTask::class => ControllerEventsTask::class,
		ViewEventsTask::class => ViewEventsTask::class,
	];

	/**
	 * @var array<\IdeHelper\CodeCompletion\Task\TaskInterface>
	 */
	protected $tasks;

	/**
	 * @param array<string|\IdeHelper\Generator\Task\TaskInterface> $tasks
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
	 * @param \IdeHelper\CodeCompletion\Task\TaskInterface|string $task The task to map.
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	protected function add($task) {
		if (is_string($task)) {
			$task = new $task();
		}

		$class = get_class($task);
		if (!$task instanceof TaskInterface) {
			throw new InvalidArgumentException(
				sprintf('Cannot use `%s` as task, it is not implementing `%s`.', $class, TaskInterface::class),
			);
		}

		$this->tasks[$class] = $task;

		return $this;
	}

	/**
	 * @return array<\IdeHelper\CodeCompletion\Task\TaskInterface>
	 */
	public function tasks(): array {
		return $this->tasks;
	}

	/**
	 * @return array<string, array<string>>
	 */
	public function getMap(): array {
		$map = [];
		foreach ($this->tasks as $task) {
			$snippet = $task->create();
			if (!$snippet) {
				continue;
			}

			$map[$task->type()][] = $snippet;
		}

		ksort($map);

		return $map;
	}

}
