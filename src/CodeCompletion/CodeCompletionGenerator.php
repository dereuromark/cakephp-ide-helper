<?php

namespace IdeHelper\CodeCompletion;

use Cake\Core\Configure;
use RuntimeException;

class CodeCompletionGenerator {

	protected TaskCollection $taskCollection;

	/**
	 * @param \IdeHelper\CodeCompletion\TaskCollection $taskCollection
	 */
	public function __construct(TaskCollection $taskCollection) {
		$this->taskCollection = $taskCollection;
	}

	/**
	 * @return array<string>
	 */
	public function generate(): array {
		$map = $this->taskCollection->getMap();

		foreach ($map as $namespace => $array) {
			$content = $this->buildContent($array);

			$template = <<<CODE
<?php
namespace $namespace;

/**
 * Only for code completion - regenerate using `bin/cake code_completion generate`.
 */
$content
CODE;

			$path = $this->path();
			$filename = $path . 'CodeCompletion' . $this->type($namespace) . '.php';

			if (!file_exists($filename) || md5_file($filename) !== md5($template)) {
				if (file_put_contents($filename, $template) === false) {
					throw new RuntimeException(sprintf('Failed to write file `%s`.', $filename));
				}
			}
		}

		return array_keys($map);
	}

	/**
	 * @param array<string> $array
	 *
	 * @return string
	 */
	protected function buildContent(array $array): string {
		return implode('', $array);
	}

	/**
	 * @param string $namespace
	 *
	 * @return string
	 */
	protected function type(string $namespace): string {
		return (string)preg_replace('/[^\da-z]/i', '', $namespace);
	}

	/**
	 * @throws \RuntimeException When the directory cannot be created.
	 * @return string
	 */
	protected function path(): string {
		$path = Configure::read('IdeHelper.codeCompletionPath') ?: TMP;
		if (!is_dir($path) && !mkdir($path, 0770, true) && !is_dir($path)) {
			throw new RuntimeException(sprintf('Cannot create directory `%s`.', $path));
		}

		return $path;
	}

}
