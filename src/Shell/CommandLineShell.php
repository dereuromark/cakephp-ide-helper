<?php
/**
 * PhpStorm Command Line Tools Console Builder for CakePHP by @skie
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Xml;

/**
 * Command Line Tools Console Builder for CakePHP, for PhpStorm and IDEs that understand
 * schemas/frameworkDescriptionVersion1.1.3.xsd
 */
class CommandLineShell extends Shell {

	/**
	 * Path to the CakePHP shell script.
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * PhpStorm alias for cake shells.
	 *
	 * @var string
	 */
	protected $_alias = 'ck';

	/**
	 * Commands list for available shells.
	 *
	 * @var array
	 */
	protected $_commands = [];

	/**
	 * Contains tasks to load and instantiate
	 *
	 * @var array
	 */
	public $tasks = ['Command'];

	/**
	 * Starts up the Shell and displays the welcome message.
	 *
	 * @return void
	 */
	public function startup(): void {
		$argv = env('argv');
		$this->_path = $argv[0];
	}

	/**
	 * @return void
	 */
	public function main(): void {
		$shells = $this->_listAvailableShells();
		foreach ($shells as $shell) {
			$this->_scanShell($shell['call_as']);
		}
		$this->_save();
	}

	/**
	 * Saves generated file to .idea/commandlinetools/ folder.
	 *
	 * @return void
	 */
	protected function _save(): void {
		$path = ROOT . DS . '.idea' . DS . 'commandlinetools' . DS . 'Custom_' . $this->_alias . '.xml';
		$File = new File($path, true);
		$File->write($this->_toXml());
		$File->close();
	}

	/**
	 * Scan all available shells and returns names list.
	 *
	 * @return array
	 */
	protected function _listAvailableShells(): array {
		$shellList = $this->Command->getShellList();
		$plugins = Plugin::loaded();
		$shells = [];
		foreach ($shellList as $plugin => $commands) {
			foreach ($commands as $command) {
				$callable = $command;
				if (in_array($plugin, $plugins)) {
					$callable = Inflector::camelize($plugin) . '.' . $command;
				}

				$shells[] = [
					'name' => $command,
					'call_as' => $callable,
					'provider' => $plugin,
					'help' => $callable . ' -h',
				];
			}
		}

		return $shells;
	}

	/**
	 * Scan shell for all available commands and remember it.
	 *
	 * @param string $fullName
	 * @return void
	 */
	protected function _scanShell($fullName): void {
		$fullName = Inflector::camelize($fullName);
		$className = App::className($fullName, 'Shell', 'Shell');
		$Shell = new $className($this->_io);
		$Shell->params['requested'] = true;
		$Shell->initialize();
		$Shell->startup();
		$Shell->OptionParser = $Shell->getOptionParser();

		if ($fullName != 'Bake') {
			foreach ($Shell->taskNames as $task) {
				$taskParser = $Shell->{$task}->getOptionParser();
				$subcommands = $Shell->OptionParser->subcommands();
				$taskName = Inflector::underscore($task);
				if (isset($subcommands[$taskName])) {
					continue;
				}
				$Shell->OptionParser->addSubcommand($taskName, [
					'help' => $taskParser->description(),
					'parser' => $taskParser,
				]);
			}
		}
		$this->_addCommand($Shell->OptionParser->toArray(), $Shell->OptionParser);
	}

	/**
	 * Register command from a shell with all available options.
	 *
	 * @param array $data
	 * @param \Cake\Console\ConsoleOptionParser $optionParser
	 * @return void
	 */
	protected function _addCommand($data, $optionParser): void {
		$command = Hash::get($data, 'command');
		$description = Hash::get($data, 'description');
		$fullHelp = $optionParser->help(null, 'text');
		$help = $description;
		if ($fullHelp) {
			$help = $fullHelp;
		}
		$help = str_replace("\n", '<br/>', $help);
		$result = [
			'name' => $command,
			'help' => $help,
			'params' => $this->_parseParams($data),
			'optionsBefore' => $this->_parseOptions($data),
		];
		$this->_commands[] = $result;
		foreach ($data['subcommands'] as $subCommand) {
			$name = $subCommand->name();
			$help = $subCommand->help();
			$parser = $subCommand->parser();
			$fullCommand = $command . ' ' . $name;
			if ($parser) {
				$subCommandData = $parser->toArray();
				$fullCommand = $command . ' ' . $subCommandData['command'];
				$subCommandData['command'] = $fullCommand;
				$this->_addCommand($subCommandData, $parser);
			} else {
				$result = [
					'name' => $fullCommand,
					'help' => $help,
					'params' => $this->_parseParams($data),
					'optionsBefore' => $this->_parseOptions($data),
				];
				$this->_commands[] = $result;
			}
		}
	}

	/**
	 * Builds shell options description.
	 *
	 * @param array $data
	 * @return array
	 */
	protected function _parseOptions($data): array {
		$options = Hash::get($data, 'options');
		$result = [];
		foreach ($options as $option) {
			$name = $option->name();
			$short = $option->short();
			if (strpos($name, '-') === false) {
				$name = '--' . $name;
			}
			if (strpos($name, '-') === false) {
				$short = '-' . $short;
			}
			$opt = [
				'@name' => $name,
				'@shortcut' => $short,
				'help' => $option->help(20),
			];
			$result[] = $opt;
		}

		return ['option' => $result];
	}

	/**
	 * Builds shell arguments description.
	 *
	 * @param array $data
	 * @return string
	 */
	protected function _parseParams($data): string {
		$arguments = Hash::get($data, 'arguments');
		$result = [];
		foreach ($arguments as $argument) {
			$name = $argument->name();
			//$help = $argument->help();
			$result[] .= $name . '[=null]';
		}

		return implode(' ', $result);
	}

	/**
	 * Returns shell xml file top structure.
	 *
	 * @return string
	 */
	protected function _toXml() {
		$xml = [
			'framework' => [
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'@xsi:noNamespaceSchemaLocation' => 'schemas/frameworkDescriptionVersion1.1.3.xsd',
				'@frameworkId' => 'cakephp',
				'@name' => 'CakePHP 4',
				'@invoke' => '"$PhpExecutable$" ' . $this->_path,
				'@alias' => $this->_alias,
				'@enabled' => 'true',
				'@version' => 2,
				'command' => $this->_commands,
			],
		];
		$Xml = Xml::fromArray($xml);

		return $Xml->asXML();
	}

}
