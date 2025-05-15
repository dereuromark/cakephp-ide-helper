<?php

namespace IdeHelper\CodeCompletion;

use Cake\Core\Configure;

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
				file_put_contents($filename, $template);
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
	 * @return string
	 */
	protected function path(): string {
		$path = Configure::read('IdeHelper.codeCompletionPath') ?: TMP;
		if (!is_dir($path)) {
			mkdir($path, 0770, true);
		}

		return $path;
	}

}
