<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;

abstract class AnnotateCommand extends Command {

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
		parent::execute($args, $io);

		if ($args->getOption('ci')) {
			if (!$args->getOption('dry-run') || $args->getOption('interactive')) {
				$io->error('Continuous Integration mode requires -d param as well as no -i param!');
				$this->abort();
			}
		}
	}

	/**
	 * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
	 *
	 * @return \Cake\Console\ConsoleOptionParser The built parser.
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$options = [
			'dry-run' => [
				'short' => 'd',
				'help' => 'Dry run the task(s). Don\'t modify any files.',
				'boolean' => true,
			],
			'plugin' => [
				'short' => 'p',
				'help' => 'The plugin(s) to run. Defaults to the application otherwise. Supports wildcard `*` for partial match, `all` for all app plugins.',
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
			'interactive' => [
				'short' => 'i',
				'help' => 'Interactive mode (prompt before each type).',
				'boolean' => true,
			],
		];

		$parser->addOptions($options);

		return $parser->setDescription('Annotation Command for generating better IDE auto-complete/hinting.');
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io(): Io {
		assert($this->io !== null, 'IO not set');

		return new Io($this->io);
	}

	/**
	 * @param string $fileName
	 *
	 * @return bool
	 */
	protected function _shouldSkip(string $fileName): bool {
		assert($this->args !== null, 'Args not set');

		$filter = (string)$this->args->getOption('filter');
		if (!$filter) {
			return false;
		}

		return !preg_match('/' . preg_quote($filter, '/') . '/i', $fileName);
	}

	/**
	 * Checks template extensions against whitelist.
	 *
	 * @param string $extension
	 * @return bool
	 */
	protected function _shouldSkipExtension(string $extension): bool {
		$whitelist = Configure::read('IdeHelper.templateExtensions') ?: static::TEMPLATE_EXTENSIONS;

		return !in_array($extension, $whitelist, true);
	}

	/**
	 * @param class-string<\IdeHelper\Annotator\AbstractAnnotator> $class
	 *
	 * @return \IdeHelper\Annotator\AbstractAnnotator
	 */
	protected function getAnnotator(string $class): AbstractAnnotator {
		/** @phpstan-var array<class-string<\IdeHelper\Annotator\AbstractAnnotator>> $tasks */
		$tasks = (array)Configure::read('IdeHelper.annotators');
		if (isset($tasks[$class])) {
			$class = $tasks[$class];
		}

		if (!isset($this->_instantiatedAnnotators[$class])) {
			assert($this->args !== null, 'Args not set');

			$options = $this->args->getOptions();
			$options['plugin'] = $this->plugin;

			$this->_instantiatedAnnotators[$class] = new $class($this->_io(), $options);
		}

		return $this->_instantiatedAnnotators[$class];
	}

	/**
	 * @return bool
	 */
	protected function _annotatorMadeChanges(): bool {
		return AbstractAnnotator::$output !== false;
	}

}
