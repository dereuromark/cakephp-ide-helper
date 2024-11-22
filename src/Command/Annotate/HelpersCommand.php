<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Command\AnnotateCommand;

class HelpersCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate used helpers inside helpers.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$paths = $this->getPaths('View/Helper');
		foreach ($paths as $path) {
			$this->_helpers($path);
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
	protected function _helpers($folder) {
		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {
			if (is_dir($path)) {
				$this->_helpers($path);
			} else {
				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
				$annotator = $this->getAnnotator(HelperAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
