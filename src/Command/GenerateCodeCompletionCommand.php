<?php

namespace IdeHelper\Command;

use Cake\Command\Command;
use Cake\Console\ConsoleOptionParser;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;

class GenerateCodeCompletionCommand extends Command {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'CodeCompletion File Generator for generating better IDE auto-complete/hinting.';
	}

	/**
	 *
	 * @throws \Cake\Console\Exception\StopException
	 * @return int The exit code or null for success
	 */
	public function execute(): int {
		$args = $this->args;
		$io = $this->io;
		$codeCompletionGenerator = $this->getGenerator();

		if ($args->getOption('dry-run')) {
			return static::CODE_SUCCESS;
		}

		$types = $codeCompletionGenerator->generate();

		$io->out('CodeCompletion files generated: ' . implode(', ', $types));

		return static::CODE_SUCCESS;
	}

	/**
	 * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
	 *
	 * @return \Cake\Console\ConsoleOptionParser The built parser.
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$parser = parent::buildOptionParser($parser);
		$options = [
			'dry-run' => [
				'short' => 'd',
				'help' => 'Dry run the generation. This will not actually generate any files.',
				'boolean' => true,
			],
		];

		return $parser
			->setDescription(static::getDescription())
			->addOptions($options);
	}

	/**
	 * @return \IdeHelper\CodeCompletion\CodeCompletionGenerator
	 */
	protected function getGenerator(): CodeCompletionGenerator {
		$taskCollection = new TaskCollection();

		return new CodeCompletionGenerator($taskCollection);
	}

}
