<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use IdeHelper\Annotator\CallbackAnnotator;
use IdeHelper\Command\AnnotateCommand;

class CallbacksCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate callback methods using callback annotation tasks. This task is not part of `all` when `-r` is used.';
	}

	/**
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
		$parser = parent::buildOptionParser($parser);
		$parser->removeOption('remove');

		return $parser;
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$paths = $this->getPaths();
		foreach ($paths as $path) {
			$folders = glob($path . '*', GLOB_ONLYDIR) ?: [];
			foreach ($folders as $folder) {
				$this->_callbacks($folder . DS);
			}
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
	protected function _callbacks(string $folder) {
		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {
			if (is_dir($path)) {
				$folderName = pathinfo($path, PATHINFO_BASENAME);
				$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

				if ($prefixes !== null && !in_array($folderName, $prefixes, true)) {
					continue;
				}

				$this->_callbacks($path . DS);
			} else {
				$extension = pathinfo($path, PATHINFO_EXTENSION);
				if ($extension !== 'php') {
					continue;
				}

				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);

				$annotator = $this->getAnnotator(CallbackAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
