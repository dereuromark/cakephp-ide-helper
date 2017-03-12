<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Annotator\ShellAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;
use PHP_CodeSniffer;
use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Fixer;
use PHP_CodeSniffer_Tokens;

/**
 * Shell for improving IDE support.
 *
 * @author Mark Scherer
 * @license MIT
 */
class AnnotationsShell extends Shell {

	/**
	 * @return void
	 */
	public function startup() {
		parent::startup();
	}

	/**
	 * @return void
	 */
	public function models() {
		$plugin = $this->param('plugin');
		$folders = App::path('Model/Table', $plugin);

		foreach ($folders as $folder) {
			$this->_models($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _models($folder) {
		$this->out($folder, 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read();

		$count = 0;
		foreach ($folderContent[1] as $file) {
			$annotator = new ModelAnnotator($this->_io(), $this->params);

			$result = $annotator->annotate($folder . $file);
			if ($result) {
				$count++;
			}
		}
	}

	/**
	 * @return void
	 */
	public function controllers() {
		$plugin = $this->param('plugin');
		$folders = App::path('Controller', $plugin);

		foreach ($folders as $folder) {
			$this->_controllers($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _controllers($folder) {
		$this->out($folder, 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read();

		foreach ($folderContent[1] as $file) {
			$this->out(' * ' . $file, 1, Shell::VERBOSE);
			$annotator = new ControllerAnnotator($this->_io(), $this->params);
			$annotator->annotate($folder . $file);
		}

		if (!empty($folderContent[0]) && in_array('Admin', $folderContent[0])) {
			$this->_controllers($folder . 'Admin' . DS);
			return;
		}
	}

	/**
	 * @return void
	 */
	public function templates() {
		$plugin = $this->param('plugin');
		$folders = App::path('Template', $plugin);

		foreach ($folders as $folder) {
			$this->_templates($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _templates($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		$this->out(str_replace(APP, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out(' * ' . $name, 1, Shell::VERBOSE);
			$annotator = new TemplateAnnotator($this->_io(), $this->params);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_templates($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function helpers() {
		$plugin = $this->param('plugin');
		$folders = App::path('View/Helper', $plugin);

		foreach ($folders as $folder) {
			$this->_helpers($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _helpers($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		$this->out(str_replace(APP, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out(' * ' . $name, 1, Shell::VERBOSE);
			$annotator = new HelperAnnotator($this->_io(), $this->params);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_helpers($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function shells() {
		$plugin = $this->param('plugin');
		$folders = App::path('Shell', $plugin);

		foreach ($folders as $folder) {
			$this->_shells($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _shells($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, false, true);

		$this->out(str_replace(APP, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out(' * ' . $name, 1, Shell::VERBOSE);
			$annotator = new ShellAnnotator($this->_io(), $this->params);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_shells($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function view() {
		if ($this->param('plugin')) {
			$this->abort('Plugin not supported for this command');
		}

		//TODO: Improve finding the correct one by introspecting loadHelper() calls and $helpers config.
		$className = App::className('App', 'View', 'View');
		$file = APP . 'View' . DS . 'AppView.php';
		if (!$className || !file_exists($file)) {
			$this->abort('You need to create `AppView.php` first in `src/View/`.');
		}

		$annotator = new ViewAnnotator($this->_io(), $this->params);
		$annotator->annotate($file);
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser() {
		$subcommandParser = [
			'options' => [
				'dry-run' => [
					'short' => 'd',
					'help' => 'Dry run the task',
					'boolean' => true,
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'Plugin',
					'default' => null,
				],
			]
		];

		return parent::getOptionParser()
			->setDescription('Annotation Shell for better IDE auto-complete/hinting')
			->addSubcommand('models', [
				'help' => 'Annotate fields and relations in table and entity class',
				'parser' => $subcommandParser
			])->addSubcommand('controllers', [
				'help' => 'Annotate primary model as well as used models in controller class',
				'parser' => $subcommandParser
			])->addSubcommand('templates', [
				'help' => 'Annotate helpers in view templates and elements',
				'parser' => $subcommandParser
			])->addSubcommand('view', [
				'help' => 'Annotate used helpers in AppView',
				'parser' => $subcommandParser
			])->addSubcommand('helpers', [
				'help' => 'Annotate used helpers inside helpers',
				'parser' => $subcommandParser
			])->addSubcommand('shells', [
				'help' => 'Annotate primary model as well as used models in shells',
				'parser' => $subcommandParser
			]);
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io() {
		return new Io($this->io());
	}

}
