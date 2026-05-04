<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use IdeHelper\Annotator\ClassAnnotator;
use IdeHelper\Annotator\ClassAnnotatorTask\PathAwareClassAnnotatorTaskInterface;
use IdeHelper\Annotator\ClassAnnotatorTaskCollection;
use IdeHelper\Command\AnnotateCommand;

class ClassesCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate classes using class annotation tasks. This task is not part of "all" when "-r" is used.';
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

		$paths = $this->getPaths('classes');
		foreach ($paths as $plugin => $pluginPaths) {
			$this->setPlugin($plugin);
			foreach ($pluginPaths as $path) {
				$folders = glob($path . '*', GLOB_ONLYDIR) ?: [];
				foreach ($folders as $folder) {
					$this->_classes($folder . DS);
				}
			}
		}

		$collection = new ClassAnnotatorTaskCollection();
		$tasks = $collection->defaultTasks();

		$this->_walkPathAwareTasks($tasks);

		if ($args->getOption('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * Walk every directory declared by a path-aware annotator task, in app
	 * context and (when `-p <plugin>` is used) per-plugin. The standard
	 * ClassAnnotator runs over each *.php it finds; tasks gate themselves
	 * via shouldRun(), so unrelated tasks self-skip these paths.
	 *
	 * @param array<class-string<\IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface>> $tasks
	 * @return void
	 */
	protected function _walkPathAwareTasks(array $tasks): void {
		$pathAware = array_filter(
			$tasks,
			fn (string $cls): bool => is_a($cls, PathAwareClassAnnotatorTaskInterface::class, true),
		);
		if (!$pathAware) {
			return;
		}

		$paths = $this->getPaths();
		$walked = [];
		foreach ($paths as $plugin => $pluginPaths) {
			$this->setPlugin($plugin);
			foreach ($pluginPaths as $rootPath) {
				foreach ($pathAware as $taskClass) {
					foreach ($taskClass::scanPaths() as $relPath) {
						$folder = $rootPath . trim($relPath, '/' . DS) . DS;
						if (isset($walked[$folder]) || !is_dir($folder)) {
							continue;
						}
						$walked[$folder] = true;
						$this->_classes($folder);
					}
				}
			}
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _classes(string $folder) {
		$this->io->out(str_replace(ROOT . DS, '', $folder), 1, ConsoleIo::VERBOSE);

		$folderContent = glob($folder . '*') ?: [];
		foreach ($folderContent as $path) {

			// Prevent infinite loop
			if ($folder === $path) {
				continue;
			}

			if (is_dir($path)) {
				$folderName = pathinfo($path, PATHINFO_BASENAME);
				$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

				if ($prefixes !== null && !in_array($folderName, $prefixes, true)) {
					continue;
				}

				$this->_classes($path . DS);
			} else {
				$extension = pathinfo($path, PATHINFO_EXTENSION);
				if ($extension !== 'php') {
					continue;
				}

				$name = pathinfo($path, PATHINFO_FILENAME);
				if ($this->_shouldSkip($name, $path)) {
					continue;
				}

				$this->io->out('-> ' . $name, 1, ConsoleIo::VERBOSE);

				$annotator = $this->getAnnotator(ClassAnnotator::class);
				$annotator->annotate($path);
			}
		}
	}

}
