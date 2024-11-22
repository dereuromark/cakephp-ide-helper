<?php

namespace IdeHelper\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\CallbackAnnotator;
use IdeHelper\Annotator\ClassAnnotator;
use IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTaskCollection;
use IdeHelper\Annotator\CommandAnnotator;
use IdeHelper\Annotator\ComponentAnnotator;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Annotator\RoutesAnnotator;
use IdeHelper\Annotator\ShellAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\PluginPath;

/**
 * Shell for improving IDE support.
 *
 * @author Mark Scherer
 * @license MIT
 */
class AnnotationsShell extends Shell {

	/**
	 * @var int
	 */
	public const CODE_CHANGES = 2;

	/**
	 * @var array<string>
	 */
	public const TEMPLATE_EXTENSIONS = ['php'];

	/**
	 * @var array<string, mixed>
	 */
	protected $_config = [
		'skipTemplatePaths' => [
			'/templates/Bake/',
		],
	];

	/**
	 * @var array<string, \IdeHelper\Annotator\AbstractAnnotator>
	 */
	protected $_instantiatedAnnotators = [];

	/**
	 * @return void
	 */
	public function startup(): void {
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
	 * @return int
	 */
	public function callbacks() {
		$paths = $this->getPaths('classes');
		foreach ($paths as $path) {
			if (!is_dir($path)) {
				continue;
			}

			$folder = new Folder($path);

			$folders = $folder->subdirectories();
			foreach ($folders as $folder) {
				$this->_callbacks($folder . DS);
			}
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
		];
		if (!$this->param('plugin') && !$this->param('filter')) {
			$types[] = 'view';
		}
		$types[] = 'templates';

		if ($this->param('remove')) {
			$this->verbose('Skipping "routes, "classes" and "callbacks" annotations, they do not support removing.');
		} else {
			$types[] = 'routes';
			$types[] = 'classes';
			$types[] = 'callbacks';
		}

		if (!$this->param('interactive')) {
			$this->interactive = false;
		}

		$changes = false;
		foreach ($types as $key => $type) {
			if ($key !== 0) {
				$this->out('');
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

			if ($this->_annotatorMadeChanges()) {
				$changes = true;
			}
		}

		if ($this->param('ci') && $changes) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * @return int
	 */
	public function models() {
		$paths = $this->getPaths('Model/Table');
		foreach ($paths as $path) {
			$this->_models($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
	 */
	public function classes() {
		$paths = $this->getPaths('Model/Table');
		foreach ($paths as $path) {
			if (!is_dir($path)) {
				continue;
			}

			$folder = new Folder($path);
			$folders = $folder->subdirectories();
			foreach ($folders as $folder) {
				$this->_classes($folder . DS);
			}
		}

		$collection = new ClassAnnotatorTaskCollection();
		$tasks = $collection->defaultTasks();
		if (!in_array(TestClassAnnotatorTask::class, $tasks, true)) {
			return static::CODE_SUCCESS;
		}

		$paths = $this->getPaths();
		foreach ($paths as $path) {
			$path .= 'tests' . DS . 'TestCase' . DS;
			if (!is_dir($path)) {
				continue;
			}

			$folder = new Folder($path);
			$folders = $folder->subdirectories();
			foreach ($folders as $folder) {
				$this->_classes($folder . DS);
			}
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
	 */
	public function controllers() {
		$paths = $this->getPaths('Controller');

		foreach ($paths as $path) {
			$this->_controllers($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
			if ($subFolder === 'Component') {
				continue;
			}

			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$this->_controllers($folder . $subFolder . DS);
		}
	}

	/**
	 * @return int
	 */
	public function routes() {
		$paths = $this->getPaths();

		foreach ($paths as $path) {
			$name = 'routes.php';
			$path .= 'config' . DS . $name;
			if (!file_exists($path)) {
				continue;
			}

			$this->out('-> ' . $name, 1, Shell::VERBOSE);
			$annotator = $this->getAnnotator(RoutesAnnotator::class);
			$annotator->annotate($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * @return int
	 */
	public function templates() {
		$paths = $this->getPaths('templates');

		foreach ($paths as $path) {
			$this->_templates($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
			$templatePathPos = strpos($folder, DS . 'templates' . DS);
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
	 * @return int
	 */
	public function helpers() {
		$paths = $this->getPaths('View/Helper');

		foreach ($paths as $path) {
			$this->_helpers($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
	 */
	public function components() {
		$paths = $this->getPaths('Controller/Component');

		foreach ($paths as $path) {
			$this->_components($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
	 */
	public function commands() {
		$paths = $this->getPaths('Command');

		foreach ($paths as $path) {
			$this->_commands($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
	 */
	public function shells() {
		$paths = $this->getPaths('Shell');

		foreach ($paths as $path) {
			$this->_shells($path);
		}

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
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
	 * @return int
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
			$this->warn('You need to create `AppView.php` first in `' . APP_DIR . DS . 'View' . DS . '`.');

			return static::CODE_SUCCESS;
		}

		$folder = pathinfo($file, PATHINFO_DIRNAME);
		$this->out(str_replace(ROOT, '', $folder));
		$this->out(' -> ' . pathinfo($file, PATHINFO_BASENAME));

		$annotator = $this->getAnnotator(ViewAnnotator::class);
		$annotator->annotate($file);

		if ($this->param('ci') && $this->_annotatorMadeChanges()) {
			return static::CODE_CHANGES;
		}

		return static::CODE_SUCCESS;
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$subcommandParser = [
			'options' => [
				'dry-run' => [
					'short' => 'd',
					'help' => 'Dry run the task(s). Don\'t modify any files.',
					'boolean' => true,
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'The plugin(s) to run. Defaults to the application otherwise. Supports wildcard `*` for partial match, `all` for all app plugins.',
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
				'ci' => [
					'help' => 'Enable CI mode (requires dry-run). This will return an error code ' . static::CODE_CHANGES . ' if changes are necessary.',
					'boolean' => true,
				],
			],
		];

		$parserWithoutRemove = $subcommandParser;
		unset($parserWithoutRemove['options']['remove']);

		$allParser = $subcommandParser;
		$allParser['options']['interactive'] = [
			'short' => 'i',
			'help' => 'Interactive mode (prompt before each type).',
			'boolean' => true,
		];

		return parent::getOptionParser()
			->setDescription('Annotation Shell for generating better IDE auto-complete/hinting.')
			->addSubcommand('all', [
				'help' => 'Annotate all supported classes.',
				'parser' => $allParser,
			])->addSubcommand('models', [
				'help' => 'Annotate fields and relations in table and entity class.',
				'parser' => $subcommandParser,
			])->addSubcommand('controllers', [
				'help' => 'Annotate primary model as well as used models in controller class.',
				'parser' => $subcommandParser,
			])->addSubcommand('templates', [
				'help' => 'Annotate helpers in view templates and elements.',
				'parser' => $subcommandParser,
			])->addSubcommand('view', [
				'help' => 'Annotate used helpers in AppView.',
				'parser' => $subcommandParser,
			])->addSubcommand('components', [
				'help' => 'Annotate used components inside components.',
				'parser' => $subcommandParser,
			])->addSubcommand('helpers', [
				'help' => 'Annotate used helpers inside helpers.',
				'parser' => $subcommandParser,
			])->addSubcommand('commands', [
				'help' => 'Annotate primary model as well as used models in commands.',
				'parser' => $subcommandParser,
			])->addSubcommand('shells', [
				'help' => 'Annotate primary model as well as used models in shells. Also annotates tasks.',
				'parser' => $subcommandParser,
			])->addSubcommand('routes', [
				'help' => 'Annotate routes file.',
				'parser' => $subcommandParser,
			])->addSubcommand('classes', [
				'help' => 'Annotate classes using class annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove,
			])->addSubcommand('callbacks', [
				'help' => 'Annotate callback methods using callback annotation tasks. This task is not part of "all" when "-r" is used.',
				'parser' => $parserWithoutRemove,
			]);
	}

	/**
	 * @return \IdeHelper\Console\Io
	 */
	protected function _io(): Io {
		return new Io($this->getIo());
	}

	/**
	 * @param string $fileName
	 *
	 * @return bool
	 */
	protected function _shouldSkip($fileName): bool {
		$filter = (string)$this->param('filter');
		if (!$filter) {
			return false;
		}

		return !preg_match('/' . preg_quote($filter, '/') . '/i', $fileName);
	}

	/**
	 * Checks template extensions against whitelist.
	 *
	 * @param string $extension
	 * @return bool
	 */
	protected function _shouldSkipExtension($extension): bool {
		$whitelist = Configure::read('IdeHelper.templateExtensions') ?: static::TEMPLATE_EXTENSIONS;

		return !in_array($extension, $whitelist, true);
	}

	/**
	 * @phpstan-param class-string<\IdeHelper\Annotator\AbstractAnnotator> $class
	 *
	 * @param string $class
	 *
	 * @return \IdeHelper\Annotator\AbstractAnnotator
	 */
	protected function getAnnotator($class): AbstractAnnotator {
		/** @phpstan-var array<class-string<\IdeHelper\Annotator\AbstractAnnotator>> $tasks */
		$tasks = (array)Configure::read('IdeHelper.annotators');
		if (isset($tasks[$class])) {
			$class = $tasks[$class];
		}

		if (!isset($this->_instantiatedAnnotators[$class])) {
			$this->_instantiatedAnnotators[$class] = new $class($this->_io(), $this->params);
		}

		return $this->_instantiatedAnnotators[$class];
	}

	/**
	 * @return bool
	 */
	protected function _annotatorMadeChanges(): bool {
		return AbstractAnnotator::$output !== false;
	}

	/**
	 * @param string|null $type
	 * @return array<string>
	 */
	protected function getPaths(?string $type = null): array {
		$plugin = (string)$this->param('plugin') ?: null;
		if (!$plugin) {
			if (!$type) {
				return [ROOT . DS];
			}

			if ($type === 'classes') {
				return [ROOT . DS . APP_DIR . DS];
			}

			return $type === 'templates' ? App::path('templates') : AppPath::get($type);
		}

		$plugins = $this->getPlugins($plugin);

		$paths = [];
		foreach ($plugins as $plugin) {
			if (!$type) {
				$pluginPaths = [Plugin::path($plugin)];
			} else {
				if ($type === 'classes') {
					$pluginPaths = [PluginPath::classPath($plugin)];
				} else {
					$pluginPaths = $type === 'templates' ? App::path('templates', $plugin) : AppPath::get($type, $plugin);
				}
			}
			foreach ($pluginPaths as $pluginPath) {
				$paths[] = $pluginPath;
			}
		}

		return $paths;
	}

	/**
	 * @param string $plugin
	 *
	 * @return array<string>
	 */
	protected function getPlugins(string $plugin): array {
		if (strpos($plugin, '*') === false) {
			return [Plugin::path($plugin) => $plugin];
		}

		$loaded = Plugin::loaded();
		$plugins = [];
		foreach ($loaded as $name) {
			$path = Plugin::path($name);
			$rootPath = str_replace(ROOT . DS, '', $path);
			if (strpos($rootPath, 'vendor' . DS) === 0) {
				continue;
			}
			$plugins[$path] = $name;
		}

		if ($plugin === 'all') {
			return $plugins;
		}

		return $this->filterPlugins($loaded, $plugin);
	}

	/**
	 * @param array<string> $plugins
	 * @param string $pattern
	 * @return array<string>
	 */
	protected function filterPlugins(array $plugins, string $pattern): array {
		return array_filter($plugins, function($plugin) use ($pattern) {
			return fnmatch($pattern, $plugin);
		});
	}

}
