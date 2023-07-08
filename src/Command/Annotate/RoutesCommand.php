<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Plugin;
use IdeHelper\Annotator\RoutesAnnotator;
use IdeHelper\Command\AnnotateCommand;

class RoutesCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate routes file.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$plugin = (string)$args->getOption('plugin') ?: null;
		$path = $plugin ? Plugin::path($plugin) : ROOT . DS;

		$name = 'routes.php';
		$path .= 'config' . DS . $name;
		if (!file_exists($path)) {
			return static::CODE_SUCCESS;
		}

		$this->io?->out('-> ' . $name, 1, ConsoleIo::VERBOSE);
		$annotator = $this->getAnnotator(RoutesAnnotator::class);
		$annotator->annotate($path);

		if ($args->getOption('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

}
