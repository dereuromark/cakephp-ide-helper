<?php

namespace IdeHelper\Generator;

use Cake\Core\Configure;
use IdeHelper\Generator\Task\BehaviorTask;
use IdeHelper\Generator\Task\CacheTask;
use IdeHelper\Generator\Task\CellTask;
use IdeHelper\Generator\Task\ComponentTask;
use IdeHelper\Generator\Task\ConfigureTask;
use IdeHelper\Generator\Task\ConnectionTask;
use IdeHelper\Generator\Task\ConsoleHelperTask;
use IdeHelper\Generator\Task\ConsoleTask;
use IdeHelper\Generator\Task\DatabaseTableColumnNameTask;
use IdeHelper\Generator\Task\DatabaseTableColumnTypeTask;
use IdeHelper\Generator\Task\DatabaseTableTask;
use IdeHelper\Generator\Task\DatabaseTypeTask;
use IdeHelper\Generator\Task\ElementTask;
use IdeHelper\Generator\Task\EntityTask;
use IdeHelper\Generator\Task\EnvTask;
use IdeHelper\Generator\Task\FixtureTask;
use IdeHelper\Generator\Task\FormHelperTask;
use IdeHelper\Generator\Task\HelperTask;
use IdeHelper\Generator\Task\LayoutTask;
use IdeHelper\Generator\Task\MailerTask;
use IdeHelper\Generator\Task\ModelTask;
use IdeHelper\Generator\Task\PluginTask;
use IdeHelper\Generator\Task\RequestTask;
use IdeHelper\Generator\Task\RoutePathTask;
use IdeHelper\Generator\Task\TableAssociationTask;
use IdeHelper\Generator\Task\TableFinderTask;
use IdeHelper\Generator\Task\TaskInterface;
use IdeHelper\Generator\Task\TranslationKeyTask;
use IdeHelper\Generator\Task\ValidationTask;
use InvalidArgumentException;

class TaskCollection {

	/**
	 * @phpstan-var array<class-string<\IdeHelper\Generator\Task\TaskInterface>, class-string<\IdeHelper\Generator\Task\TaskInterface>>
	 *
	 * @var array<string, string>
	 */
	protected $defaultTasks = [
		ModelTask::class => ModelTask::class,
		BehaviorTask::class => BehaviorTask::class,
		ComponentTask::class => ComponentTask::class,
		HelperTask::class => HelperTask::class,
		MailerTask::class => MailerTask::class,
		TableAssociationTask::class => TableAssociationTask::class,
		TableFinderTask::class => TableFinderTask::class,
		DatabaseTypeTask::class => DatabaseTypeTask::class,
		ElementTask::class => ElementTask::class,
		LayoutTask::class => LayoutTask::class,
		PluginTask::class => PluginTask::class,
		ValidationTask::class => ValidationTask::class,
		RoutePathTask::class => RoutePathTask::class,
		CacheTask::class => CacheTask::class,
		RequestTask::class => RequestTask::class,
		EnvTask::class => EnvTask::class,
		ConsoleTask::class => ConsoleTask::class,
		ConnectionTask::class => ConnectionTask::class,
		FormHelperTask::class => FormHelperTask::class,
		DatabaseTableTask::class => DatabaseTableTask::class,
		DatabaseTableColumnNameTask::class => DatabaseTableColumnNameTask::class,
		DatabaseTableColumnTypeTask::class => DatabaseTableColumnTypeTask::class,
		EntityTask::class => EntityTask::class,
		FixtureTask::class => FixtureTask::class,
		TranslationKeyTask::class => TranslationKeyTask::class,
		ConfigureTask::class => ConfigureTask::class,
		CellTask::class => CellTask::class,
		ConsoleHelperTask::class => ConsoleHelperTask::class,
	];

	/**
	 * @var array<\IdeHelper\Generator\Task\TaskInterface>
	 */
	protected $tasks;

	/**
	 * @param array<string|\IdeHelper\Generator\Task\TaskInterface> $tasks
	 */
	public function __construct(array $tasks = []) {
		$defaultTasks = $this->defaultTasks();
		$tasks += $defaultTasks;

		foreach ($tasks as $task) {
			if (!$task) {
				continue;
			}

			$this->add($task);
		}
	}

	/**
	 * @phpstan-return array<class-string<\IdeHelper\Generator\Task\TaskInterface>>
	 *
	 * @return array<string>
	 */
	protected function defaultTasks(): array {
		$tasks = (array)Configure::read('IdeHelper.generatorTasks') + $this->defaultTasks;

		foreach ($tasks as $k => $v) {
			if (is_numeric($k)) {
				$tasks[$v] = $v;
				unset($tasks[$k]);
			}
		}

		return $tasks;
	}

	/**
	 * Adds a task to the collection.
	 *
	 * @param \IdeHelper\Generator\Task\TaskInterface|string $task The task to map.
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
	 * @return array<\IdeHelper\Generator\Task\TaskInterface>
	 */
	public function tasks(): array {
		return $this->tasks;
	}

	/**
	 * @return array<\IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function getMap(): array {
		$map = [];
		foreach ($this->tasks as $task) {
			$map += $task->collect();
		}

		ksort($map);

		return $map;
	}

}
