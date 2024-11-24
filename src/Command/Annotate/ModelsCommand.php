<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Command\AnnotateCommand;

class ModelsCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate fields and relations in table and entity class.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$paths = $this->getPaths('Model/Table');
		foreach ($paths as $plugin => $pluginPaths) {
			foreach ($pluginPaths as $path) {
				$this->setPlugin($plugin);
				$this->_models($path);
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
	protected function _models(string $folder) {
		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $file) {
			if (!is_file($file)) {
				continue;
			}
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);

			$annotator = $this->getAnnotator(ModelAnnotator::class);
			$annotator->annotate($file);
		}
	}

}
