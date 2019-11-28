<?php

namespace IdeHelper\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;

/**
 * Shell for generating generic IDE auto-completion files.
 *
 * @author Mark Scherer
 * @license MIT
 */
class CodeCompletionShell extends Shell {

	/**
	 * Generates CodeCompletation.php files.
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
	public function getOptionParser(): ConsoleOptionParser {
		$subcommandParser = [
			'options' => [
				'dry-run' => [
					'short' => 'd',
					'help' => 'Dry run the generation. This will not actually generate any files.',
					'boolean' => true,
				],
			],
		];

		return parent::getOptionParser()
			->setDescription('CodeCompletion File Generator for generating better IDE auto-complete/hinting.')
			->addSubcommand('generate', [
				'help' => 'Generates `/tmp/CodeCompletion{type}.php` files.',
				'parser' => $subcommandParser,
			]);
	}

	/**
	 * @return \IdeHelper\CodeCompletion\CodeCompletionGenerator
	 */
	protected function getGenerator(): CodeCompletionGenerator {
		$taskCollection = new TaskCollection();

		return new CodeCompletionGenerator($taskCollection);
	}

}
