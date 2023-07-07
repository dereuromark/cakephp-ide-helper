<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\App;
use IdeHelper\Annotator\CommandAnnotator;
use IdeHelper\Command\AnnotateCommand;

class CommandsCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate primary model as well as used models in commands.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$plugin = (string)$args->getOption('plugin') ?: null;
		$folders = App::classPath('Command', $plugin);

		foreach ($folders as $folder) {
			$this->_commands($folder);
		}

		if ($args->getOption('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _commands($folder) {
		$folderContent = glob($folder . '*');

		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);
		foreach ($folderContent as $file) {
			if (is_dir($file)) {
				continue;
			}
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
			$annotator = $this->getAnnotator(CommandAnnotator::class);
			$annotator->annotate($file);
		}
	}

}
