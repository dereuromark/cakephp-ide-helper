<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\CallbackAnnotator;
use IdeHelper\Annotator\ClassAnnotator;
use IdeHelper\Annotator\CommandAnnotator;
use IdeHelper\Annotator\ComponentAnnotator;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Annotator\ShellAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\PluginPath;

/**
 * Shell for improving IDE support.
 *
 * @author Mark Scherer
 * @license MIT
 */
class AnnotationsShell extends Shell {

	const CODE_CHANGES = 2;
	const TEMPLATE_EXTENSIONS = ['ctp', 'php'];

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
	 * @return void
	 */
	public function callbacks() {
		$plugin = $this->param('plugin') ?: null;

		$path = $plugin ? PluginPath::get($plugin) : ROOT . DS;

		$path .= 'src' . DS;

		$folder = new Folder($path);

		$folders = $folder->subdirectories();

		foreach ($folders as $folder) {
			$this->_callbacks($folder . DS);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _callbacks($folder) {
		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($extension !== 'php') {
				continue;
			}

			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);

			$annotator = $this->getAnnotator(CallbackAnnotator::class);
			$annotator->annotate($folder . $file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$this->_callbacks($folder . $subFolder . DS);
		}
	}

	/**
	 * @return int
	 */
	public function all() {
		$types = [
			'models',
			'controllers',
			'shells',
			'commands',
			'components',
			'helpers',
			'templates',
		];
		if (!$this->param('plugin') && !$this->param('filter')) {
			$types[] = 'view';
		}
		if ($this->param('remove')) {
			$this->verbose('Skipping "classes" and "callbacks" annotations, they do not support removing.');
		} else {
			$types[] = 'classes';
			$types[] = 'callbacks';
		}

		if (!$this->param('interactive')) {
			$this->interactive = false;
		}

		$changes = false;
		foreach ($types as $key => $type) {
			if ($key !== 0) {
				$this->out();
			}
			$typeName = Inflector::humanize($type);
			if (!$this->param('interactive')) {
				$this->out('[' . $typeName . ']');
			}
			$in = $this->in($typeName . '?', ['y', 'n', 'a'], 'y');
			if (!$this->interactive && $in === null) {
				$in = 'y';
			}

			if ($in === 'a') {
				$this->abort('Aborted');
			}
			if ($in !== 'y') {
				continue;
			}

			$this->$type();

			if (AbstractAnnotator::$output !== false) {
				$changes = true;
			}
		}

		if ($this->param('ci') && $changes) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * @return void
	 */
	public function models() {
		$plugin = $this->param('plugin') ?: null;
		$folders = AppPath::get('Model/Table', $plugin);

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

		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);

			$annotator = $this->getAnnotator(ModelAnnotator::class);
			$annotator->annotate($folder . $file);
		}
	}

	/**
	 * @return void
	 */
	public function classes() {
		$plugin = $this->param('plugin') ?: null;

		$path = $plugin ? PluginPath::get($plugin) : ROOT . DS;

		$path .= 'src' . DS;

		$folder = new Folder($path);

		$folders = $folder->subdirectories();

		foreach ($folders as $folder) {
			$this->_classes($folder . DS);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _classes($folder) {
		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($extension !== 'php') {
				continue;
			}

			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);

			$annotator = $this->getAnnotator(ClassAnnotator::class);
			$annotator->annotate($folder . $file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$this->_classes($folder . $subFolder . DS);
		}
	}

	/**
	 * @return void
	 */
	public function controllers() {
		$plugin = $this->param('plugin') ?: null;
		$folders = AppPath::get('Controller', $plugin);

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
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);

			$annotator = $this->getAnnotator(ControllerAnnotator::class);
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
		$folders = AppPath::get('Template', $plugin);

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
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($this->_shouldSkipExtension($extension)) {
				continue;
			}

			$name = pathinfo($file, PATHINFO_FILENAME);
			$dir = $name;
			$templatePathPos = strpos($folder, 'src' . DS . 'Template' . DS);
			if ($templatePathPos) {
				$dir = substr($folder, $templatePathPos + 13) . DS . $name;
			}
			if ($this->_shouldSkip($dir)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(TemplateAnnotator::class);
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
		$folders = AppPath::get('View/Helper', $plugin);

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
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(HelperAnnotator::class);
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
		$folders = AppPath::get('Controller/Component', $plugin);

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
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(ComponentAnnotator::class);
			$annotator->annotate($file);
		}

		foreach ($folderContent[0] as $subFolder) {
			$this->_components($subFolder);
		}
	}

	/**
	 * @return void
	 */
	public function commands() {
		$plugin = $this->param('plugin') ?: null;
		$folders = AppPath::get('Command', $plugin);

		foreach ($folders as $folder) {
			$this->_commands($folder);
		}
	}

	/**
	 * @return void
	 */
	public function shells() {
		$plugin = $this->param('plugin') ?: null;
		$folders = AppPath::get('Shell', $plugin);

		foreach ($folders as $folder) {
			$this->_shells($folder);
		}
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _commands($folder) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true, true);

		$this->out(str_replace(ROOT, '', $folder), 1, Shell::VERBOSE);
		foreach ($folderContent[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(CommandAnnotator::class);
			$annotator->annotate($file);
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
			if ($this->_shouldSkip($name)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(ShellAnnotator::class);
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
		if ($this->param('filter')) {
			$this->abort('Filter option not supported for this command');
		}

		$className = App::className('App', 'View', 'View');
		$file = APP . 'View' . DS . 'AppView.php';
		if (!$className || !file_exists($file)) {
			$this->warn('You need to create `AppView.php` first in `src/View/`.');
			return;
		}

		$folder = pathinfo($file, PATHINFO_DIRNAME);
		$this->out(str_replace(ROOT, '', $folder));
		$this->out(' -> ' . pathinfo($file, PATHINFO_BASENAME));

		$annotator = $this->getAnnotator(ViewAnnotator::class);
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
					'help' => 'Dry run the task(s). Don\'t modify any files.',
					'boolean' => true,
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'The plugin to run. Defaults to the application otherwise.',
					'default' => null,
				],
				'remove' => [
					'short' => 'r',
					'help' => 'Remove outdated annotations. Make sure you commited first or have a backup!',
					'boolean' => true,
				],
				'filter' => [
					'short' => 'f',
					'help' => 'Filter by search string in file name. For templates also in path.',
					'default' => null,
				],
			]
		];

		$parserWithoutRemove = $subcommandParser;
		unset($parserWithoutRemove['options']['remove']);

		$allParser = $subcommandParser;
		$allParser['options']['interactive'] = [
			'short' => 'i',
			'help' => 'Interactive mode (prompt before each type).',
			'boolean' => true,
		];
		$allParser['options']['ci'] = [
			'help' => 'Enable CI mode (requires dry-run). This will return an error code ' . static::CODE_CHANGES . ' if changes are necessary.',
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
			])->addSubcommand('commands', [
				'help' => 'Annotate primary model as well as used models in commands.',
				'parser' => $subcommandParser
			])->addSubcommand('shells', [
				'help' => 'Annotate primary model as well as used models in shells. Also annotates tasks.',
				'parser' => $subcommandParser
			])->addSubcommand('classes', [
				'help' => 'Annotate classes using class annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove
			])->addSubcommand('callbacks', [
				'help' => 'Annotate callback methods using callback annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove
			]);
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io() {
		return new Io($this->getIo());
	}

	/**
	 * @param string $fileName
	 *
	 * @return bool
	 */
	protected function _shouldSkip($fileName) {
		$filter = $this->param('filter');
		if (!$filter) {
			return false;
		}

		return !(bool)preg_match('/' . preg_quote($filter, '/') . '/i', $fileName);
	}

	/**
	 * Checks template extensions against whitelist.
	 *
	 * @param string $extension
	 * @return bool
	 */
	protected function _shouldSkipExtension($extension) {
		$whitelist = Configure::read('IdeHelper.templateExtensions') ?: static::TEMPLATE_EXTENSIONS;

		return !in_array($extension, $whitelist, true);
	}

	/**
	 * @param string $class
	 *
	 * @return \IdeHelper\Annotator\AbstractAnnotator
	 */
	protected function getAnnotator($class) {
		$tasks = (array)Configure::read('IdeHelper.annotators');
		if (isset($tasks[$class])) {
			$class = $tasks[$class];
		}

		return new $class($this->_io(), $this->params);
	}

}
