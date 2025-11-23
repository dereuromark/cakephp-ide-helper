<?php

namespace IdeHelper\Illuminator\Task;

use Cake\Core\Configure;
use IdeHelper\Annotator\Traits\DocBlockTrait;
use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Files\File;

/**
 * Adds $defaultTable = '' property to controllers that don't have a corresponding table class.
 */
class ControllerDefaultTableTask extends AbstractTask {

	use FileTrait;
	use DocBlockTrait;

	/**
	 * @var array<string, mixed>
	 */
	protected array $_defaultConfig = [
		'fastPropertyCheck' => false,
	];

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if (!str_ends_with($className, 'Controller')) {
			return false;
		}

		if ($className === 'AppController' || preg_match('#[A-Za-z0-9]AppController$#', $className)) {
			return false;
		}

		if (!preg_match('#[\\\/]Controller[\\\/]#', $path)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $content
	 * @param string $path Path to file.
	 * @return string
	 */
	public function run(string $content, string $path): string {
		$file = $this->getFile('', $content);
		$classIndex = $file->findNext(T_CLASS, 0);
		if (!$classIndex) {
			return $content;
		}

		if ($this->hasDefaultTableProperty($file, $classIndex, $content)) {
			return $content;
		}

		$className = pathinfo($path, PATHINFO_FILENAME);
		$namespace = $this->extractNamespace($content);
		if (!$namespace) {
			return $content;
		}

		$baseNamespace = $this->detectPluginFromNamespace($namespace);

		$modelName = substr($className, 0, -10);
		$modelClassName = str_replace('/', '\\', $baseNamespace) . '\\Model\\Table\\' . $modelName . 'Table';

		if (class_exists($modelClassName)) {
			return $content;
		}

		return $this->addDefaultTableProperty($file, $classIndex, $content);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $classIndex
	 * @param string $content
	 * @return bool
	 */
	protected function hasDefaultTableProperty(File $file, int $classIndex, string $content): bool {
		// Fast regex check for performance (optional, can be enabled via config)
		if ($this->getConfig('fastPropertyCheck')) {
			if (preg_match('/\bprotected \?string \$defaultTable\b/', $content)) {
				return true;
			}

			return false;
		}

		// Robust token-based check within class scope
		$tokens = $file->getTokens();

		$openBraceIndex = $tokens[$classIndex]['scope_opener'];
		$closeBraceIndex = $tokens[$classIndex]['scope_closer'];
		if (!$openBraceIndex || !$closeBraceIndex) {
			return false;
		}

		for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] === '$defaultTable') {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $classIndex
	 * @param string $content
	 * @return string
	 */
	protected function addDefaultTableProperty(File $file, int $classIndex, string $content): string {
		$tokens = $file->getTokens();

		$openBraceIndex = $tokens[$classIndex]['scope_opener'];
		$closeBraceIndex = $tokens[$classIndex]['scope_closer'];
		if (!$openBraceIndex || !$closeBraceIndex) {
			return $content;
		}

		$indentation = Configure::read('IdeHelper.illuminatorIndentation') ?? "\t";

		$fixer = $this->getFixer($file);

		$property = PHP_EOL . PHP_EOL . $indentation . 'protected ?string $defaultTable = \'\';';

		$fixer->addContent($openBraceIndex, $property);

		return $fixer->getContents();
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @return string
	 */
	protected function detectIndentation(File $file, int $index): string {
		$tokens = $file->getTokens();

		$line = $tokens[$index]['line'];
		$firstOfLine = $index;
		while ($firstOfLine - 1 >= 0 && $tokens[$firstOfLine - 1]['line'] === $line) {
			$firstOfLine--;
		}

		$whitespace = '';
		for ($i = $firstOfLine; $i < $index; $i++) {
			if ($tokens[$i]['code'] === T_WHITESPACE) {
				$whitespace .= $tokens[$i]['content'];
			}
		}

		return $whitespace;
	}

	/**
	 * @param string $content
	 * @return string|null
	 */
	protected function extractNamespace(string $content): ?string {
		if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
			return $matches[1];
		}

		return null;
	}

	/**
	 * @param string $namespace
	 * @return string
	 */
	protected function detectPluginFromNamespace(string $namespace): string {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		if ($plugin) {
			return $plugin;
		}

		$parts = explode('\\', $namespace);
		$controllerIndex = array_search('Controller', $parts, true);
		if ($controllerIndex !== false && $controllerIndex > 0) {
			return implode('\\', array_slice($parts, 0, $controllerIndex));
		}

		return Configure::read('App.namespace', 'App');
	}

}
