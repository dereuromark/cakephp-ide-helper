<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\TestSuite\ConnectionHelper;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use RuntimeException;

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
			ConnectionHelper::addTestAliases();
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
			'file' => [
				'help' => 'Pass file(s) to run for, comma separated. Can be absolute or ROOT relative.',
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
		return new Io($this->io);
	}

	/**
	 * @param string $fileName
	 * @param string|null $path
	 *
	 * @return bool
	 */
	protected function _shouldSkip(string $fileName, ?string $path = null): bool {
		$files = $this->_files();
		if ($files) {
			return !in_array($path, $files, true);
		}

		$filter = (string)$this->args->getOption('filter');
		if (!$filter) {
			return false;
		}

		return !preg_match('/' . preg_quote($filter, '/') . '/i', $fileName);
	}

	/**
	 * @return array<string>
	 */
	protected function _files(): array {
		$file = (string)$this->args->getOption('file');
		if (!$file) {
			return [];
		}

		$files = explode(',', $file);
		foreach ($files as $k => $file) {
			if (!str_starts_with($file, ROOT . DS)) {
				$file = ROOT . DS . $file;
			}

			if (!file_exists($file)) {
				throw new RuntimeException('Cannot find file: ' . $file);
			}

			$files[$k] = $file;
		}

		return $files;
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

		$options = $this->args->getOptions();
		$options['plugin'] = $this->plugin;

		return new $class($this->_io(), $options);
	}

	/**
	 * @return bool
	 */
	protected function _annotatorMadeChanges(): bool {
		return AbstractAnnotator::$output !== false;
	}

}
