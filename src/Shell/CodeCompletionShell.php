<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;
use RuntimeException;

/**
 * Shell for generating a generic IDE auto-completion file.
 *
 * @author Mark Scherer
 * @license MIT
 */
class CodeCompletionShell extends Shell {

	const CODE_CHANGES = 2;

	/**
	 * @return void
	 */
	public function startup() {
		parent::startup();
	}

	/**
	 * Generates .phpstorm.meta.php file.
	 *
	 * @return int
	 */
	public function generate() {
		$codeCompletionGenerator = $this->getGenerator();

		if ($this->param('dry-run')) {
			return static::CODE_SUCCESS;
		}

		$types = $codeCompletionGenerator->generate();

		$this->out('CodeCompletion files generated: ' . implode(', ', $types));

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
					'help' => 'Dry run the task. This will not actually generate any files.',
					'boolean' => true,
				],
			]
		];

		return parent::getOptionParser()
			->setDescription('CodeCompletion File Generator for generating better IDE auto-complete/hinting.')
			->addSubcommand('generate', [
				'help' => 'Generates `/tmp/CodeCompletion{type}.php` files.',
				'parser' => $subcommandParser
			]);
	}

	/**
	 * @return \IdeHelper\CodeCompletion\CodeCompletionGenerator
	 */
	protected function getGenerator() {
		$taskCollection = new TaskCollection();

		return new CodeCompletionGenerator($taskCollection);
	}

	/**
	 * @return string
	 */
	protected function getMetaFilePath() {
		if (is_file(ROOT . DS . '.phpstorm.meta.php')) {
			throw new RuntimeException('Please use a directory called `ROOT/.phpstorm.meta.php/` and store your custom files there. Remove any root file you still have.');
		}

		return ROOT . DS . '.phpstorm.meta.php' . DS . '.ide-helper.meta.php';
	}

}
