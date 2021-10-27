<?php

namespace IdeHelper\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
use IdeHelper\Utility\PluginPath;
use InvalidArgumentException;

/**
 * Shell for modifying your PHP files based on Illuminator rulesets.
 *
 * @author Mark Scherer
 * @license MIT
 */
class IlluminatorShell extends Shell {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * @param string|null $path
	 * @throws \InvalidArgumentException
	 * @return int
	 */
	public function illuminate($path = null) {
		if (!$path) {
			$path = ($this->param('plugin') ? 'src' : APP_DIR) . DS;
		}

		$root = ROOT . DS;
		if ($this->param('plugin')) {
			$root = PluginPath::get((string)$this->param('plugin'));
		}
		$path = $root . $path;
		if (!is_dir($path)) {
			throw new InvalidArgumentException('Path does not exist: ' . $path);
		}

		$illuminator = $this->getIlluminator();
		$filesChanged = $illuminator->illuminate($path, (string)$this->param('filter') ?: null);
		if (!$filesChanged) {
			return static::CODE_SUCCESS;
		}

		if ($this->param('dry-run')) {
			$this->out($filesChanged . ' files need(s) updating.');

			return static::CODE_CHANGES;
		}

		$this->out('Files updated: ' . $filesChanged);

		return static::CODE_SUCCESS;
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$tasks = $this->getTaskList();

		$subcommandParser = [
			'options' => [
				'plugin' => [
					'short' => 'p',
					'help' => 'The plugin to run. Defaults to the application otherwise.',
					'default' => null,
				],
				'dry-run' => [
					'short' => 'd',
					'help' => 'Dry run the task(s). This will output an error code ' . static::CODE_CHANGES . ' if file needs changing. Can be used for CI checking.',
					'boolean' => true,
				],
				'task' => [
					'short' => 't',
					'help' => 'Run specific task(s). Can be comma separated list. Available: ' . implode(', ', $tasks),
					'default' => null,
				],
				'filter' => [
					'short' => 'f',
					'help' => 'Filter by search string in file name.',
					'default' => null,
				],
			],
			'arguments' => [
				'path' => [
					'name' => 'path',
					'help' => 'Path in your project or plugin. Defaults to src/',
					'required' => false,
				],
			],
		];

		$taskList = 'Tasks: ' . implode(', ', $tasks);

		return parent::getOptionParser()
			->setDescription('Illuminator PHP File Modifier.')
			->addSubcommand('illuminate', [
				'help' => 'Run Illuminator tasks over your PHP files.' . PHP_EOL . $taskList,
				'parser' => $subcommandParser,
			]);
	}

	/**
	 * @return \IdeHelper\Illuminator\Illuminator
	 */
	protected function getIlluminator(): Illuminator {
		$tasks = $this->param('task') ? explode(',', (string)$this->param('task')) : [];

		$taskCollection = new TaskCollection($this->_io(), $this->params, $tasks);

		return new Illuminator($taskCollection);
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io(): Io {
		return new Io($this->getIo());
	}

	/**
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return array<string>
	 */
	protected function getTaskList(): array {
		$taskCollection = new TaskCollection($this->_io(), $this->params);

		return $taskCollection->taskNames();
	}

}
