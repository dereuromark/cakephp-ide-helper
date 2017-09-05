<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\TaskCollection;

/**
 * Shell for generating PHPStorm specific IDE meta file.
 *
 * @author Mark Scherer
 * @license MIT
 */
class PhpstormShell extends Shell {

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
		$taskCollection = new TaskCollection();
		$phpstormGenerator = new PhpstormGenerator($taskCollection);

		$content = $phpstormGenerator->generate();

		$file = ROOT . DS . '.phpstorm.meta.php';

		$currentContent = file_get_contents($file);
		if ($content === $currentContent) {
			$this->out('Meta file `.phpstorm.meta.php` still up to date.');
			return parent::CODE_SUCCESS;
		}

		file_put_contents($file, $content);

		$this->out('Meta file `.phpstorm.meta.php` generated.');

		return static::CODE_CHANGES;
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser() {
		$subcommandParser = [
		];

		return parent::getOptionParser()
			->setDescription('Meta File Generator for generating better IDE auto-complete/hinting in PHPStorm.')
			->addSubcommand('generate', [
				'help' => 'Generate `.phpstorm.meta.php` meta file.',
				'parser' => $subcommandParser
			]);
	}

}
