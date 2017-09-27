<?php
namespace IdeHelper\CodeCompletion;

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
	 * @return string[]
	 */
	public function generate() {
		$map = $this->taskCollection->getMap();

		foreach ($map as $namespace => $array) {
			$content = $this->buildContent($array);

			$template = <<<TXT
<?php
namespace $namespace;

exit('Only for code completion - regenerate using `bin/cake code_completion generate`.');

$content
TXT;

			$filename = TMP . 'CodeCompletion' . $this->type($namespace) . '.php';

			file_put_contents($filename, $template);
		}

		return array_keys($map);
	}

	/**
	 * @param array $array
	 *
	 * @return string
	 */
	protected function buildContent(array $array) {
		return implode('', $array);
	}

	/**
	 * @param string $namespace
	 *
	 * @return string
	 */
	protected function type($namespace) {
		return preg_replace('/[^\da-z]/i', '', $namespace);
	}

}
