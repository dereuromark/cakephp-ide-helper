<?php

namespace IdeHelper\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\TaskCollection;
use RuntimeException;

/**
 * Shell for generating PhpStorm specific IDE meta file.
 *
 * @author Mark Scherer
 * @license MIT
 */
class PhpstormShell extends Shell {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * Generates .phpstorm.meta.php file.
	 *
	 * @return int
	 */
	public function generate() {
		$phpstormGenerator = $this->getGenerator();
		$content = $phpstormGenerator->generate();

		$file = $this->getMetaFilePath();

		$currentContent = file_exists($file) ? file_get_contents($file) : null;
		if ($content === $currentContent) {
			$this->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.');

			return parent::CODE_SUCCESS;
		}

		if ($this->param('dry-run')) {
			$this->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` needs updating.');

			return static::CODE_CHANGES;
		}

		$this->ensureDir();
		file_put_contents($file, $content);

		$this->out('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.');

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
					'help' => 'Dry run the generation. This will output an error code ' . static::CODE_CHANGES . ' if file needs changing. Can be used for CI checking.',
					'boolean' => true,
				],
			],
		];

		return parent::getOptionParser()
			->setDescription('Meta File Generator for generating better IDE auto-complete/hinting in PhpStorm.')
			->addSubcommand('generate', [
				'help' => 'Generate `/.phpstorm.meta.php/.ide-helper.meta.php` meta file.',
				'parser' => $subcommandParser,
			]);
	}

	/**
	 * @return \IdeHelper\Generator\PhpstormGenerator
	 */
	protected function getGenerator(): PhpstormGenerator {
		$taskCollection = new TaskCollection();

		return new PhpstormGenerator($taskCollection, $this->getIo());
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

}
