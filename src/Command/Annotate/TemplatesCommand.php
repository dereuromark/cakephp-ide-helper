<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Command\AnnotateCommand;

class TemplatesCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate helpers in view templates and elements.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$paths = $this->getPaths('templates');

		foreach ($paths as $plugin => $pluginPaths) {
			$this->setPlugin($plugin);
			foreach ($pluginPaths as $path) {
				$this->_templates($path);
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
	protected function _templates($folder) {
		$this->io->out(str_replace(ROOT, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {

			// Prevent infinite loop
			if ($folder === $path) {
				continue;
			}

			if (is_dir($path)) {
				foreach ($this->_config['skipTemplatePaths'] as $skip) {
					$subFolder = pathinfo($path, PATHINFO_BASENAME);
					if (!str_contains($subFolder, $skip)) {
						continue;
					}

					if ($this->args->getOption('verbose')) {
						$this->io->warning(sprintf('Skipped template folder `%s`', str_replace(ROOT, '', $subFolder)));
					}

					break;
				}
				$this->_templates($path . DS);
			} else {
				$extension = pathinfo($path, PATHINFO_EXTENSION);
				if ($this->_shouldSkipExtension($extension)) {
					continue;
				}

				$name = pathinfo($path, PATHINFO_FILENAME);
				$dir = $name;
				$templatePathPos = strpos($folder, DS . 'templates' . DS);
				if ($templatePathPos) {
					$dir = substr($folder, $templatePathPos + 13) . DS . $name;
				}
				if ($this->_shouldSkip($dir, $path)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
				$annotator = $this->getAnnotator(TemplateAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
