<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\Plugin;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
use InvalidArgumentException;

/**
 * Shell for modifying your PHP files based on Illuminator rulesets.
 *
 * @author Mark Scherer
 * @license MIT
 */
class IlluminatorShell extends Shell {

	const CODE_CHANGES = 2;

	/**
	 * @param string|null $path
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function illuminate($path = null) {
		if (!$path) {
			$path = 'src/';
		}

		$root = ROOT . DS;
		if ($this->param('plugin')) {
			$root = Plugin::path($this->param('plugin'));
		}
		$path = $root . $path;
		if (!is_dir($path)) {
			throw new InvalidArgumentException('Path does not exist: ' . $path);
		}

		$illuminator = $this->getIlluminator();
		$filesChanged = $illuminator->illuminate($path);
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
	public function getOptionParser() {
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
			],
			'arguments' => [
				'path' => [
					'name' => 'path',
					'help' => 'Path in your project or plugin. Defaults to src/',
					'required' => false,
				],
			]
		];

		return parent::getOptionParser()
			->setDescription('Illuminator PHP File Modifier.')
			->addSubcommand('illuminate', [
				'help' => 'Run Illuminator tasks over your PHP files.',
				'parser' => $subcommandParser
			]);
	}

	/**
	 * @return \IdeHelper\Illuminator\Illuminator
	 */
	protected function getIlluminator() {
		$tasks = $this->param('task') ? explode(',', $this->param('task')) : [];

		$taskCollection = new TaskCollection($this->_io(), $this->params, $tasks);

		return new Illuminator($taskCollection);
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io() {
		return new Io($this->getIo());
	}

	/**
	 * @return string[]
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	protected function getTaskList() {
		$taskCollection = new TaskCollection($this->_io(), $this->params);

		return $taskCollection->taskNames();
	}

}
