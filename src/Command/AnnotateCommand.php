<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Shim\Command\Command;

class AnnotateCommand extends Command {

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
	//protected string $name = 'annotate';

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
					'help' => 'Dry run the task(s). Don\'t modify any files.',
					'boolean' => true,
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'The plugin to run. Defaults to the application otherwise.',
					'default' => null,
				],
				'remove' => [
					'short' => 'r',
					'help' => 'Remove outdated annotations. Make sure you commited first or have a backup!',
					'boolean' => true,
				],
				'filter' => [
					'short' => 'f',
					'help' => 'Filter by search string in file name. For templates also in path.',
					'default' => null,
				],
				'ci' => [
					'help' => 'Enable CI mode (requires dry-run). This will return an error code ' . static::CODE_CHANGES . ' if changes are necessary.',
					'boolean' => true,
				],
			],
		];

		$parserWithoutRemove = $subcommandParser;
		unset($parserWithoutRemove['options']['remove']);

		$allParser = $subcommandParser;
		$allParser['options']['interactive'] = [
			'short' => 'i',
			'help' => 'Interactive mode (prompt before each type).',
			'boolean' => true,
		];

		return parent::getOptionParser()
			->setDescription('Annotation Command for generating better IDE auto-complete/hinting.')
			->addSubcommand('all', [
				'help' => 'Annotate all supported classes.',
				'parser' => $allParser,
			])->addSubcommand('models', [
				'help' => 'Annotate fields and relations in table and entity class.',
				'parser' => $subcommandParser,
			])->addSubcommand('controllers', [
				'help' => 'Annotate primary model as well as used models in controller class.',
				'parser' => $subcommandParser,
			])->addSubcommand('templates', [
				'help' => 'Annotate helpers in view templates and elements.',
				'parser' => $subcommandParser,
			])->addSubcommand('view', [
				'help' => 'Annotate used helpers in AppView.',
				'parser' => $subcommandParser,
			])->addSubcommand('components', [
				'help' => 'Annotate used components inside components.',
				'parser' => $subcommandParser,
			])->addSubcommand('helpers', [
				'help' => 'Annotate used helpers inside helpers.',
				'parser' => $subcommandParser,
			])->addSubcommand('commands', [
				'help' => 'Annotate primary model as well as used models in commands.',
				'parser' => $subcommandParser,
			])->addSubcommand('routes', [
				'help' => 'Annotate routes file.',
				'parser' => $subcommandParser,
			])->addSubcommand('classes', [
				'help' => 'Annotate classes using class annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove,
			])->addSubcommand('callbacks', [
				'help' => 'Annotate callback methods using callback annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove,
			]);
	}

}
