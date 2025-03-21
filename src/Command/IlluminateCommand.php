<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;

class IlluminateCommand extends Command {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'PHP file modifier.';
	}

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
		parent::execute($args, $io);

		$paths = $this->getPaths();

		$pathElement = $args->getArgument('path');
		if (!$pathElement) {
			$pathElement = ($args->getOption('plugin') ? 'src' : APP_DIR) . DS;
		}

		$filesChanged = 0;
		foreach ($paths as $plugin => $pluginPaths) {
			$this->setPlugin($plugin);
			foreach ($pluginPaths as $path) {
				$path .= $pathElement;
				if (!is_dir($path)) {
					continue;
				}

				$illuminator = $this->getIlluminator($args);
				$filesChanged += $illuminator->illuminate($path, (string)$args->getOption('filter') ?: null);
			}
		}

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
				'help' => 'The plugin(s) to run. Defaults to the application otherwise. Supports wildcard `*` for partial match, `all` for all app plugins.',
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
		$descr = static::getDescription() . PHP_EOL . 'Run Illuminator tasks over your PHP files.' . PHP_EOL;

		return $parser->setDescription($descr . $taskList);
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 *
	 * @return \IdeHelper\Illuminator\Illuminator
	 */
	protected function getIlluminator(Arguments $args): Illuminator {
		$tasks = $args->getOption('task') ? explode(',', (string)$args->getOption('task')) : [];

		$options = $args->getOptions();
		$options['plugin'] = $this->plugin;

		$taskCollection = new TaskCollection($this->io(), $options, $tasks);

		return new Illuminator($taskCollection);
	}

	/**
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return array<string>
	 */
	protected function getTaskList(): array {
		$taskCollection = new TaskCollection($this->io(), []);

		return $taskCollection->taskNames();
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function io(): Io {
		$io = $this->io ?? new ConsoleIo();

		return new Io($io);
	}

}
