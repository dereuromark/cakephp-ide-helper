<?php
namespace IdeHelper\Shell;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Filesystem\Folder;
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
class AnnotationShell extends Shell {

	/**
	 * @return void
	 */
	public function startup() {
		parent::startup();
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

		$this->out('Done');
	}

	/**
	 * @param string $folder
	 * @return void
	 */
	protected function _controllers($folder) {
		$this->out($folder, 1, Shell::VERBOSE);

		$x = (new Folder($folder))->read();

		foreach ($x[1] as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			if (substr($name, -13) === 'AppController' || substr($name, -10) !== 'Controller') {
				continue;
			}

			$content = file_get_contents($folder . $file);
			if (preg_match('/@property .+Table \$/', $content)) {
				continue;
			}

			$phpcs = new PHP_CodeSniffer();
			$phpcs->process([], null, []);
			$phpcsFile = new PHP_CodeSniffer_File($file, [], [], $phpcs);
			$phpcsFile->start($content);

			$tokens = $phpcsFile->getTokens();

			$classIndex = $phpcsFile->findNext(T_CLASS, 0);

			$prevCode = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $classIndex, null, true);

			$x = $phpcsFile->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classIndex, $prevCode);
			if ($x) {
				continue;
			}

			$fixer = new PHP_CodeSniffer_Fixer();
			$fixer->startFile($phpcsFile);

			$modelName = substr($name, 0, -10);
			if (preg_match('/public \$modelClass = \'(\w+)\'/', $content, $matches)) {
				$modelName = $matches[1];
			}
			$namespace = $this->param('plugin') ?: 'App';
			$namespace = str_replace('/', '\\', $namespace);

			$docBlock = <<<PHP
/**
 * @property \\{$namespace}\\Model\\Table\\{$modelName}Table \${$modelName}
 */

PHP;

			$fixer->replaceToken($classIndex, $docBlock . $tokens[$classIndex]['content']);

			$contents = $fixer->getContents();
			$this->_storeFile($folder . $file, $contents);

			$this->out($name);
		}

		if (!empty($x[0]) && in_array('Admin', $x[0])) {
			$this->_controllers($folder . 'Admin' . DS);
			return;
		}
	}

	/**
	 * @param string $path
	 *
	 * @return array
	 */
	protected function _getTokens($path) {
		$phpcs = new PHP_CodeSniffer();
		$phpcs->process([], null, []);
		$file = $phpcs->processFile($path);
		$file->start();
		return $file->getTokens();
	}

	/**
	 * @param string $path
	 * @param string $contents
	 * @return void
	 */
	protected function _storeFile($path, $contents) {
		if ($this->param('dry-run')) {
			return;
		}
		file_put_contents($path, $contents);
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
					'boolean' => true
				],
				'plugin' => [
					'short' => 'p',
					'help' => 'Plugin',
					'default' => null,
				],
			]
		];

		return parent::getOptionParser()
			->description('Annotation Shell for better IDE auto-complete/hinting')
			->addSubcommand('controllers', [
				'help' => 'Annotate primary model in controller class',
				'parser' => $subcommandParser
			]);
	}

}
