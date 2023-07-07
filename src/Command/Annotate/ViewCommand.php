<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\App;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Command\AnnotateCommand;

class ViewCommand extends AnnotateCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate used helpers in AppView.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		if ($args->getOption('plugin')) {
			$this->abort('Plugin option not supported for this command');
		}
		if ($args->getOption('filter')) {
			$this->abort('Filter option not supported for this command');
		}

		$className = App::className('App', 'View', 'View');
		$file = APP . 'View' . DS . 'AppView.php';
		if (!$className || !file_exists($file)) {
			$io->warning('You need to create `AppView.php` first in `' . APP_DIR . DS . 'View' . DS . '`.');

			return static::CODE_SUCCESS;
		}

		$folder = pathinfo($file, PATHINFO_DIRNAME);
		$io->out(str_replace(ROOT, '', $folder));
		$io->out(' -> ' . pathinfo($file, PATHINFO_BASENAME));

		$annotator = $this->getAnnotator(ViewAnnotator::class);
		$annotator->annotate($file);

		if ($args->getOption('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

}
