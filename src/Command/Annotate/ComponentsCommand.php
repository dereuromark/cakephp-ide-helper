<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\ComponentAnnotator;
use IdeHelper\Command\AnnotateCommand;

class ComponentsCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate used components inside components.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$paths = $this->getPaths('Controller/Component');
		foreach ($paths as $plugin => $pluginPaths) {
			$this->setPlugin($plugin);
			foreach ($pluginPaths as $path) {
				$this->_components($path);
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
	protected function _components(string $folder) {
		$this->io->out(str_replace(ROOT . DS, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {
			if (is_dir($path)) {
				$this->_components($path);
			} else {
				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name, $path)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
				$annotator = $this->getAnnotator(ComponentAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
