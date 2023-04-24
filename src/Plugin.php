<?php

namespace IdeHelper;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use IdeHelper\Command\AnnotateCommand;
use IdeHelper\Command\GenerateCodeCompletionCommand;
use IdeHelper\Command\GeneratePhpStormMetaCommand;
use IdeHelper\Command\IlluminateCommand;

/**
 * Plugin for IdeHelper
 */
class Plugin extends BasePlugin {

	/**
	 * Define the console commands for an application.
	 *
	 * @param \Cake\Console\CommandCollection $commands The CommandCollection to add commands into.
	 * @return \Cake\Console\CommandCollection The updated collection.
	 */
	public function console(CommandCollection $commands): CommandCollection {
		// Add entry command to handle entry point and backwards compat.
		$commands->add('annotate', AnnotateCommand::class);
		$commands->add('code_completion', GenerateCodeCompletionCommand::class);
		$commands->add('illuminator', IlluminateCommand::class);
		$commands->add('phpstorm', GeneratePhpStormMetaCommand::class);

		return $commands;
	}

}
