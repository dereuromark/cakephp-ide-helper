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

class TaskCollection {

	const CONFIG_DRY_RUN = 'dry-run';
	const CONFIG_VISIBILITY = 'visibility';

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $_io;

	/**
	 * @var array
	 */
	protected $_config;

	/**
	 * @var string[]
	 */
	protected $defaultTasks = [
		EntityFieldTask::class => EntityFieldTask::class,
	];

	/**
	 * @var \IdeHelper\Illuminator\Task\AbstractTask[]
	 */
	protected $tasks;

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array $config
	 * @param string[] $tasks
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function __construct(Io $io, array $config, array $tasks = []) {
		$this->_io = $io;
		$this->_config = $config;

		$defaultTasks = (array)Configure::read('IdeHelper.illuminatorTasks') + $this->defaultTasks;

		$keys = array_keys($defaultTasks);
		$keyMap = array_combine($keys, $keys);
		foreach ($keyMap as $k => $v) {
			preg_match('#\bTask\\\\([A-Za-z0-9]+)Task$#', $v, $matches);
			if (!$matches) {
				throw new RuntimeException('Invalid task name: ' . $v);
			}
			$keyMap[$k] = $matches[1];
		}
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
	 * Adds a task to the collection.
	 *
	 * @param string|\IdeHelper\Illuminator\Task\AbstractTask $task The task to map.
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function add($task) {
		if (is_string($task)) {
			$task = new $task($this->_config);
		}

		$class = get_class($task);
		if (!$task instanceof AbstractTask) {
			throw new InvalidArgumentException(
				"Cannot use '$class' as task, it is not implementing " . AbstractTask::class . '.'
			);
		}

		$this->tasks[$class] = $task;

		return $this;
	}

	/**
	 * @return \IdeHelper\Illuminator\Task\AbstractTask[]
	 */
	public function tasks() {
		return $this->tasks;
	}

	/**
	 * @param string $path File path
	 * @return bool True if file is/was modified; false if nothing changed.
	 */
	public function run($path) {
		$file = str_replace(ROOT . DS, DS, $path);
		$this->_io->verbose('# ' . $file);

		$content = $result = null;

		foreach ($this->tasks as $task) {
			if (!$task->shouldRun($path)) {
				continue;
			}

			if ($content === null) {
				$content = file_get_contents($path);
				$result = $content;
			}

			$result = $task->run($result, $path);
		}

		if ($content === null || $result === $content) {
			return false;
		}

		$this->_displayDiff($content, $result);
		$this->_storeFile($path, $result, $this->_config[static::CONFIG_DRY_RUN]);

		return true;
	}

	/**
	 * @param string $oldContent
	 * @param string $newContent
	 * @return void
	 */
	protected function _displayDiff($oldContent, $newContent) {
		$differ = new Differ(null);
		$array = $differ->diffToArray($oldContent, $newContent);

		$begin = null;
		$end = null;
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
	protected function _storeFile($path, $contents, $dryRun) {
		//static::$output = true;
		if ($dryRun) {
			return;
		}

		file_put_contents($path, $contents);
	}

}
