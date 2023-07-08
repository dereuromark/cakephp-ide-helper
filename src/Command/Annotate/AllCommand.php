<?php

namespace IdeHelper\Command\Annotate;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\App;
use IdeHelper\Command\AnnotateCommand;

class AllCommand extends AnnotateCommand {

	protected bool $interactive;

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Annotate all supported classes.';
	}

	/**
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return int
	 */
	public function execute(Arguments $args, ConsoleIo $io): int {
		parent::execute($args, $io);

		$types = [
			ModelsCommand::class,
			ControllersCommand::class,
			CommandsCommand::class,
			ComponentsCommand::class,
			HelpersCommand::class,
			TemplatesCommand::class,
		];
		if (!$args->getOption('plugin') && !$args->getOption('filter')) {
			$types[] = ViewCommand::class;
		}

		if ($args->getOption('remove')) {
			$io->verbose('Skipping "routes, "classes" and "callbacks" annotations, they do not support removing.');
		} else {
			$types[] = RoutesCommand::class;
			$types[] = ClassesCommand::class;
			$types[] = CallbacksCommand::class;
		}

		if (!$args->getOption('interactive')) {
			$this->interactive = false;
		}

		$changes = false;
		foreach ($types as $key => $type) {
			if ($key !== 0) {
				$io->out('');
			}
			$shortName = App::shortName($type, 'Command', 'Command');
			$shortName = str_replace('IdeHelper.Annotate/', '', $shortName);
			if (!$this->interactive) {
				$io->out('[' . $shortName . ']');
			} else {
				$in = $io->askChoice($shortName . '?', ['y', 'n', 'a'], 'y');
				if ($in === 'a') {
					$this->abort();
				}
				if ($in !== 'y') {
					continue;
				}
			}

			$commandInstance = new $type();
			$commandInstance->execute($args, $io);

			if ($this->_annotatorMadeChanges()) {
				$changes = true;
			}
		}

		if ($args->getOption('ci') && $changes) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

}
