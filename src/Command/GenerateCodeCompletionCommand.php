<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use IdeHelper\Console\Io;
use Shim\Command\Command;

class GenerateCodeCompletionCommand extends Command {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * @var array<string>
	 */
	public const TEMPLATE_EXTENSIONS = ['php'];

	/**
	 * @var array<string, mixed>
	 */
	protected array $_config = [
		'skipTemplatePaths' => [
			'/templates/Bake/',
		],
	];

	/**
	 * @var array<string, \IdeHelper\Annotator\AbstractAnnotator>
	 */
	protected array $_instantiatedAnnotators = [];

	/**
	 * The name of this command.
	 *
	 * @var string
	 */
	//protected string $name = 'generate_code_completion';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$skip = (array)Configure::read('IdeHelper.skipTemplatePaths');
		if ($skip) {
			$this->_config['skipTemplatePaths'] = $skip;
		}
	}

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
	 * @return \IdeHelper\Console\Io
	 */
	protected function io(): Io {
		return new Io($this->io);
	}

}
