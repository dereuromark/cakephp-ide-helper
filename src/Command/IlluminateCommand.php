<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
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
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		if ($args->getOption('ci')) {
			if (!$args->getOption('dry-run') || $args->getOption('interactive')) {
				$io->error('Continuous Integration mode requires -d param as well as no -i param!');
				$this->abort();
			}
		}

		//TODO
	}

	/**
	 * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
	 *
	 * @return \Cake\Console\ConsoleOptionParser The built parser.
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
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
