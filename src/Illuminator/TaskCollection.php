<?php

namespace IdeHelper\Illuminator;

use Cake\Console\Shell;
use Cake\Core\Configure;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Task\AbstractTask;
use IdeHelper\Illuminator\Task\EntityFieldTask;
use InvalidArgumentException;
use RuntimeException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

class TaskCollection {

	/**
	 * @var string
	 */
	public const CONFIG_DRY_RUN = 'dry-run';

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $_io;

	/**
	 * @var array<string, mixed>
	 */
	protected $_config;

	/**
	 * @phpstan-var array<class-string<\IdeHelper\Illuminator\Task\AbstractTask>, class-string<\IdeHelper\Illuminator\Task\AbstractTask>>
	 *
	 * @var array<string, string>
	 */
	protected $defaultTasks = [
		EntityFieldTask::class => EntityFieldTask::class,
	];

	/**
	 * @var array<\IdeHelper\Illuminator\Task\AbstractTask>
	 */
	protected $tasks;

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 * @param array<string> $tasks
	 * @throws \InvalidArgumentException
	 */
	public function __construct(Io $io, array $config, array $tasks = []) {
		$this->_io = $io;
		$this->_config = $config;

		$defaultTasks = $this->defaultTasks();

		$keyMap = $this->taskNames($defaultTasks);
		$filterMap = array_diff($tasks, $keyMap);
		if ($filterMap) {
			throw new InvalidArgumentException('Tasks do not exist: ' . implode(', ', $filterMap) . '.');
		}

		foreach ($defaultTasks as $key => $task) {
			if (!$task) {
				continue;
			}
			$lookupKey = $keyMap[$key];
			if ($tasks && !in_array($lookupKey, $tasks, true)) {
				continue;
			}

			$this->add($task);
		}
	}

	/**
	 * @phpstan-return array<class-string<\IdeHelper\Illuminator\Task\AbstractTask>>
	 *
	 * @return array<string>
	 */
	protected function defaultTasks(): array {
		$tasks = (array)Configure::read('IdeHelper.illuminatorTasks') + $this->defaultTasks;

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
	 * @param \IdeHelper\Illuminator\Task\AbstractTask|string $task The task to map.
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	protected function add($task) {
		if (is_string($task)) {
			$task = new $task($this->_config);
		}

		$class = get_class($task);
		if (!$task instanceof AbstractTask) {
			throw new InvalidArgumentException(
				sprintf('Cannot use `%s` as task, it is not implementing `%s`.', $class, AbstractTask::class),
			);
		}

		$this->tasks[$class] = $task;

		return $this;
	}

	/**
	 * @return array<\IdeHelper\Illuminator\Task\AbstractTask>
	 */
	public function tasks(): array {
		return $this->tasks;
	}

	/**
	 * @param array<string>|array<\IdeHelper\Illuminator\Task\AbstractTask> $tasks
	 * @throws \RuntimeException
	 * @return array<string>
	 */
	public function taskNames($tasks = []): array {
		if (!$tasks) {
			$tasks = $this->tasks;
		}

		$keys = array_keys($tasks);
		$keyMap = array_combine($keys, $keys) ?: [];
		foreach ($keyMap as $k => $v) {
			preg_match('#\bTask\\\\([A-Za-z0-9]+)Task$#', $v, $matches);
			if (!$matches) {
				throw new RuntimeException('Invalid task name: ' . $v);
			}
			$keyMap[$k] = $matches[1];
		}

		return $keyMap;
	}

	/**
	 * @param string $path File path
	 * @return bool True if file is/was modified; false if nothing changed.
	 */
	public function run(string $path): bool {
		$file = str_replace(ROOT . DS, DS, $path);
		$this->_io->verbose('# ' . $file);

		$content = $result = null;

		foreach ($this->tasks as $task) {
			if (!$task->shouldRun($path)) {
				continue;
			}

			if ($content === null) {
				$content = file_get_contents($path);
				if ($content === false) {
					throw new RuntimeException('Cannot read file');
				}
				$result = $content;
			}

			$result = $task->run((string)$result, $path);
		}

		if ($content === null || $result === $content) {
			return false;
		}

		$this->displayDiff($content, (string)$result);
		$this->storeFile($path, (string)$result, $this->_config[static::CONFIG_DRY_RUN]);

		return true;
	}

	/**
	 * @param string $oldContent
	 * @param string $newContent
	 * @return void
	 */
	protected function displayDiff(string $oldContent, string $newContent): void {
		$differ = new Differ(new DiffOnlyOutputBuilder());
		$array = $differ->diffToArray($oldContent, $newContent);

		$begin = null;
		$end = null;
		/**
		 * @var int $key
		 */
		foreach ($array as $key => $row) {
			if ($row[1] === 0) {
				continue;
			}

			if ($begin === null) {
				$begin = $key;
			}
			$end = $key;
		}
		if ($begin === null) {
			return;
		}
		$firstLineOfOutput = $begin > 0 ? $begin - 1 : 0;
		$lastLineOfOutput = count($array) - 1 > $end ? $end + 1 : $end;

		for ($i = $firstLineOfOutput; $i <= $lastLineOfOutput; $i++) {
			$row = $array[$i];

			$char = ' ';
			$output = trim($row[0], "\n\r\0\x0B");

			if ($row[1] === 1) {
				$char = '+';
				$this->_io->info('   | ' . $char . $output, 1, Shell::VERBOSE);
			} elseif ($row[1] === 2) {
				$char = '-';
				$this->_io->out('<warning>' . '   | ' . $char . $output . '</warning>', 1, Shell::VERBOSE);
			} else {
				$this->_io->out('   | ' . $char . $output, 1, Shell::VERBOSE);
			}
		}
	}

	/**
	 * @param string $path
	 * @param string $contents
	 * @param bool $dryRun
	 * @return void
	 */
	protected function storeFile(string $path, string $contents, bool $dryRun): void {
		if ($dryRun) {
			return;
		}

		file_put_contents($path, $contents);
	}

}
