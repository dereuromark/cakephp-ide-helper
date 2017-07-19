<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ComponentAnnotator;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Annotator\ShellAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;

/**
 * Shell for improving IDE support.
 *
 * @author Mark Scherer
 * @license MIT
 */
class AnnotationsShell extends Shell {

	/**
	 * @var array
	 */
	protected $_config = [
		'skipTemplatePaths' => [
			'/src/Template/Bake/',
		],
	];

	/**
	 * @return void
	 */
	public function startup() {
		parent::startup();

		if ($this->param('ci')) {
			if (!$this->param('dry-run') || $this->param('interactive')) {
				$this->abort('Continuous Integration mode requires -d param as well as no -i param!');
			}
		}

		$skip = (array)Configure::read('IdeHelper.skipTemplatePaths');
		if ($skip) {
			$this->_config['skipTemplatePaths'] = $skip;
		}
	}

	/**
	 * @return bool
	 */
	public function all() {
		$types = [
			'models',
			'controllers',
			'templates',
			'shells',
			'components',
			'helpers',
		];
		if (!$this->param('plugin')) {
			$types[] = 'view';
		}

		if (!$this->param('interactive')) {
			$this->interactive = false;
		}

		foreach ($types as $key => $type) {
			if ($key !== 0) {
				$this->out();
			}
			$typeName = Inflector::humanize($type);
			if (!$this->param('interactive')) {
				$this->out('[' . $typeName . ']');
			}
			$in = $this->in($typeName . '?', ['y', 'n', 'a'], 'y');
			if ($in === 'a') {
				$this->abort('Aborted');
			}
			if ($in !== 'y') {
				continue;
			}

			$this->$type();
		}

		if ($this->param('ci')) {
			return AbstractAnnotator::$output === false;
		}

		return true;
	}

	/**
	 * @return void
	 */
	public function models() {
		$plugin = $this->param('plugin') ?: null;
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
		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		$count = 0;
		foreach ($folderContent[1] as $file) {
			$annotator = new ModelAnnotator($this->_io(), $this->params);
			$this->out('-> ' . $file, 1, Shell::VERBOSE);

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
		$plugin = $this->param('plugin') ?: null;
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
		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			$this->out('-> ' . $file, 1, Shell::VERBOSE);
			$annotator = new ControllerAnnotator($this->_io(), $this->params);
			$annotator->annotate($folder . $file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$this->_controllers($folder . $subFolder . DS);
		}
	}

	/**
	 * @return void
	 */
	public function templates() {
		$plugin = $this->param('plugin') ?: null;
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
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true, true);

		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = new TemplateAnnotator($this->_io(), $this->params);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			foreach ($this->_config['skipTemplatePaths'] as $skip) {
				if (strpos($subFolder, $skip) === false) {
					continue;
				}

				if ($this->param('verbose')) {
					$this->warn(sprintf('Skipped template folder `%s`', str_replace(ROOT, '', $subFolder)));
				}
				break 2;
			}

			$this->_templates($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function helpers() {
		$plugin = $this->param('plugin') ?: null;
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
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true, true);

		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out('-> ' . $name, 1, Shell::VERBOSE);
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
	public function components() {
		$plugin = $this->param('plugin') ?: null;
		$folders = App::path('Controller/Component', $plugin);

		foreach ($folders as $folder) {
			$this->_components($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _components($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true, true);

		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = new ComponentAnnotator($this->_io(), $this->params);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_components($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function shells() {
		$plugin = $this->param('plugin') ?: null;
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
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true, true);

		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$this->out('-> ' . $name, 1, Shell::VERBOSE);
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
			$this->abort('Plugin option not supported for this command');
		}

		//TODO: Improve finding the correct one by introspecting loadHelper() calls and $helpers config.
		$className = App::className('App', 'View', 'View');
		$file = APP . 'View' . DS . 'AppView.php';
		if (!$className || !file_exists($file)) {
			$this->warn('You need to create `AppView.php` first in `src/View/`.');
			return;
		}

		$folder = pathinfo($file, PATHINFO_DIRNAME);
		$this->out(str_replace(ROOT, '', $folder));
		$this->out(' -> ' . pathinfo($file, PATHINFO_BASENAME));

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
					'help' => 'Dry run the task. Don\'t modify any files.',
					'boolean' => true,
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'The plugin to run. Defaults to the application otherwise.',
					'default' => null,
				],
			]
		];

		$allParser = $subcommandParser;
		$allParser['options']['interactive'] = [
			'short' => 'i',
			'help' => 'Interactive mode (prompt before each type).',
			'boolean' => true,
		];
		$allParser['options']['ci'] = [
			'help' => 'Enable CI mode (requires dry-run).',
			'boolean' => true,
		];

		return parent::getOptionParser()
			->setDescription('Annotation Shell for generating better IDE auto-complete/hinting.')
			->addSubcommand('all', [
				'help' => 'Annotate all supported classes.',
				'parser' => $allParser
			])->addSubcommand('models', [
				'help' => 'Annotate fields and relations in table and entity class.',
				'parser' => $subcommandParser
			])->addSubcommand('controllers', [
				'help' => 'Annotate primary model as well as used models in controller class.',
				'parser' => $subcommandParser
			])->addSubcommand('templates', [
				'help' => 'Annotate helpers in view templates and elements.',
				'parser' => $subcommandParser
			])->addSubcommand('view', [
				'help' => 'Annotate used helpers in AppView.',
				'parser' => $subcommandParser
			])->addSubcommand('components', [
				'help' => 'Annotate used components inside components.',
				'parser' => $subcommandParser
			])->addSubcommand('helpers', [
				'help' => 'Annotate used helpers inside helpers.',
				'parser' => $subcommandParser
			])->addSubcommand('shells', [
				'help' => 'Annotate primary model as well as used models in shells.',
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
