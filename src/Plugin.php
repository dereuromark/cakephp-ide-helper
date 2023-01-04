<?php

namespace IdeHelper;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use IdeHelper\Shell\AnnotationsShell;
use IdeHelper\Shell\CodeCompletionShell;
use IdeHelper\Shell\IlluminatorShell;
use IdeHelper\Shell\PhpstormShell;

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
		$commands->add('annotate', AnnotationsShell::class);
		$commands->add('code_completion', CodeCompletionShell::class);
		$commands->add('illuminator', IlluminatorShell::class);
		$commands->add('phpstorm', PhpstormShell::class);

		return $commands;
	}

}
