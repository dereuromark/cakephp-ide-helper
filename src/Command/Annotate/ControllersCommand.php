<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\App;
use Cake\Core\Configure;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Command\AnnotateCommand;

class ControllersCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate primary model as well as used models in controller class.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$plugin = (string)$args->getOption('plugin') ?: null;
		$folders = App::classPath('Controller', $plugin);

		foreach ($folders as $folder) {
			$this->_controllers($folder);
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
	protected function _controllers($folder) {
		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*');

		foreach ($folderContent as $path) {

			if (is_dir($path)) {
				$subFolder = pathinfo($path, PATHINFO_BASENAME);
				if ($subFolder === 'Component') {
					continue;
				}

				$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

				if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
					continue;
				}

				$this->_controllers($folder . $subFolder . DS);
			} else {
				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);

				$annotator = $this->getAnnotator(ControllerAnnotator::class);
				$annotator->annotate($path);
			}

		}
	}

}
