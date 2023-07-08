<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\App;
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

		$plugin = (string)$args->getOption('plugin') ?: null;
		$folders = App::classPath('Controller/Component', $plugin);

		foreach ($folders as $folder) {
			$this->_components($folder);
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
		$this->io?->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {
			if (is_dir($path)) {
				$this->_components($path);
			} else {
				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name)) {
					continue;
				}

				$this->io?->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
				$annotator = $this->getAnnotator(ComponentAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
