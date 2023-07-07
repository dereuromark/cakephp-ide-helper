<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
use InvalidArgumentException;
use Shim\Command\Command;

class IlluminateCommand extends Command {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * E.g.:
	 * bin/cake upgrade /path/to/app --level=cakephp40
	 *
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 *
	 * @throws \Cake\Console\Exception\StopException
	 * @return int The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		$path = $args->getArgument('path');
		if (!$path) {
			$path = ($args->getOption('plugin') ? 'src' : APP_DIR) . DS;
		}

		$root = ROOT . DS;
		if ($args->getOption('plugin')) {
			$root = Plugin::path((string)$args->getOption('plugin'));
		}
		$path = $root . $path;
		if (!is_dir($path)) {
			throw new InvalidArgumentException('Path does not exist: ' . $path);
		}

		$illuminator = $this->getIlluminator();
		$filesChanged = $illuminator->illuminate($path, (string)$args->getOption('filter') ?: null);
		if (!$filesChanged) {
			return static::CODE_SUCCESS;
		}

		if ($args->getOption('dry-run')) {
			$io->out($filesChanged . ' files need(s) updating.');

			return static::CODE_CHANGES;
		}

		$io->out('Files updated: ' . $filesChanged);

		return static::CODE_SUCCESS;
	}

	/**
	 * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
	 *
	 * @return \Cake\Console\ConsoleOptionParser The built parser.
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$tasks = $this->getTaskList();

		$subcommandParser = [
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
		];

		$parser->addOptions($subcommandParser);
		$parser->addArgument('path', [
			'name' => 'path',
			'help' => 'Path in your project or plugin. Defaults to src/',
			'required' => false,
		]);

		$taskList = 'Tasks: ' . implode(', ', $tasks);
		$descr = 'Illuminator PHP File Modifier.' . PHP_EOL . 'Run Illuminator tasks over your PHP files.' . PHP_EOL;

		return $parser->setDescription($descr . $taskList);
	}

	/**
	 * @return \IdeHelper\Illuminator\Illuminator
	 */
	protected function getIlluminator(): Illuminator {
		assert($this->args !== null, 'Args not set');

		$tasks = $this->args->getOption('task') ? explode(',', (string)$this->args->getOption('task')) : [];

		$taskCollection = new TaskCollection($this->io(), $this->args->getOptions(), $tasks);

		return new Illuminator($taskCollection);
	}

	/**
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return array<string>
	 */
	protected function getTaskList(): array {
		assert($this->args !== null, 'Args not set');

		$taskCollection = new TaskCollection($this->io(), $this->args->getOptions());

		return $taskCollection->taskNames();
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function io(): Io {
		assert($this->io !== null, 'IO not set');

		return new Io($this->io);
	}

}
