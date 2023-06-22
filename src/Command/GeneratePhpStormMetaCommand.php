<?php

namespace IdeHelper\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use IdeHelper\Console\Io;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\TaskCollection;
use RuntimeException;
use Shim\Command\Command;

class GeneratePhpStormMetaCommand extends Command {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * @var array<string>
	 */
	public const TEMPLATE_EXTENSIONS = ['php'];

	/**
	 * @var array<string, \IdeHelper\Annotator\AbstractAnnotator>
	 */
	protected array $_instantiatedAnnotators = [];

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
		$phpstormGenerator = $this->getGenerator();
		$content = $phpstormGenerator->generate();

		$file = $this->getMetaFilePath();

		$currentContent = file_exists($file) ? file_get_contents($file) : null;
		if ($content === $currentContent) {
			$io->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.');

			return parent::CODE_SUCCESS;
		}

		if ($args->getOption('dry-run')) {
			$io->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` needs updating.');

			return static::CODE_CHANGES;
		}

		$this->ensureDir();
		file_put_contents($file, $content);

		$io->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.');

		return static::CODE_SUCCESS;
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
					'help' => 'Dry run the generation. This will output an error code ' . static::CODE_CHANGES . ' if file needs changing. Can be used for CI checking.',
					'boolean' => true,
				],
			],
		];

		$details = 'Generate `/.phpstorm.meta.php/.ide-helper.meta.php` meta file.';

		return $parser
			->setDescription('Meta File Generator for generating better IDE auto-complete/hinting in PhpStorm.' . PHP_EOL . $details);
	}

	/**
	 * @return \IdeHelper\Generator\PhpstormGenerator
	 */
	protected function getGenerator(): PhpstormGenerator {
		$taskCollection = new TaskCollection();

		return new PhpstormGenerator($taskCollection, $this->io());
	}

	/**
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getMetaFilePath(): string {
		if (is_file(ROOT . DS . '.phpstorm.meta.php')) {
			throw new RuntimeException('Please use a directory called `ROOT/.phpstorm.meta.php/` and store your custom files there. Remove any root file you still have.');
		}

		return ROOT . DS . '.phpstorm.meta.php' . DS . '.ide-helper.meta.php';
	}

	/**
	 * @return void
	 */
	protected function ensureDir(): void {
		if (!file_exists(dirname($this->getMetaFilePath()))) {
			mkdir(dirname($this->getMetaFilePath()), 0775, true);
		}
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function io(): Io {
		assert($this->io !== null, 'IO not set');

		return new Io($this->io);
	}

}
