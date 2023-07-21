<?php

namespace IdeHelper;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use IdeHelper\Command\Annotate\AllCommand;
use IdeHelper\Command\Annotate\CallbacksCommand;
use IdeHelper\Command\Annotate\ClassesCommand;
use IdeHelper\Command\Annotate\CommandsCommand;
use IdeHelper\Command\Annotate\ComponentsCommand;
use IdeHelper\Command\Annotate\ControllersCommand;
use IdeHelper\Command\Annotate\HelpersCommand;
use IdeHelper\Command\Annotate\ModelsCommand;
use IdeHelper\Command\Annotate\TemplatesCommand;
use IdeHelper\Command\Annotate\ViewCommand;
use IdeHelper\Command\GenerateCodeCompletionCommand;
use IdeHelper\Command\GeneratePhpStormMetaCommand;
use IdeHelper\Command\IlluminateCommand;

/**
 * Plugin for IdeHelper
 */
class IdeHelperPlugin extends BasePlugin {

	/**
	 * Define the console commands for an application.
	 *
	 * @param \Cake\Console\CommandCollection $commands The CommandCollection to add commands into.
	 * @return \Cake\Console\CommandCollection The updated collection.
	 */
	public function console(CommandCollection $commands): CommandCollection {
		$commands->add('annotate models', ModelsCommand::class);
		$commands->add('annotate view', ViewCommand::class);
		$commands->add('annotate helpers', HelpersCommand::class);
		$commands->add('annotate components', ComponentsCommand::class);
		$commands->add('annotate templates', TemplatesCommand::class);
		$commands->add('annotate controllers', ControllersCommand::class);
		$commands->add('annotate commands', CommandsCommand::class);
		$commands->add('annotate classes', ClassesCommand::class);
		$commands->add('annotate callbacks', CallbacksCommand::class);
		$commands->add('annotate all', AllCommand::class);

		$commands->add('generate code_completion', GenerateCodeCompletionCommand::class);
		$commands->add('generate phpstorm', GeneratePhpStormMetaCommand::class);
		$commands->add('illuminate', IlluminateCommand::class);

		return $commands;
	}

}
