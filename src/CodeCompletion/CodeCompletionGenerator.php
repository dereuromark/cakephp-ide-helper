<?php

namespace IdeHelper\CodeCompletion;

use Cake\Core\Configure;

class CodeCompletionGenerator {

	/**
	 * @var \IdeHelper\CodeCompletion\TaskCollection
	 */
	protected $taskCollection;

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

			file_put_contents($filename, $template);
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
