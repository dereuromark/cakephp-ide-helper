<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;

/**
 * Shell for modifying your PHP files based on Illuminator rulesets.
 *
 * @author Mark Scherer
 * @license MIT
 */
class IlluminatorShell extends Shell {

	const CODE_CHANGES = 2;

	/**
	 * Generates CodeCompletation.php files.
	 *
	 * @param string|null $path
	 * @return int
	 */
	public function illuminate($path = null) {
		if (!$path) {
			$path = 'src/';
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
		$subcommandParser = [
			'options' => [
				'dry-run' => [
					'short' => 'd',
					'help' => 'Dry run the task(s). This will output an error code ' . static::CODE_CHANGES . ' if file needs changing. Can be used for CI checking.',
					'boolean' => true,
				],
				'task' => [
					'short' => 't',
					'help' => 'Run specific task(s). Can be comma separated list.',
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

}
