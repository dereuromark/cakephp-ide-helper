<?php

namespace IdeHelper\Annotator;

use RuntimeException;

class CallbackAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$this->invokeTasks($path, $content);

		return true;
	}

	/**
	 * @param string $path
	 * @param string $content
	 *
	 * @return void
	 */
	protected function invokeTasks(string $path, string $content): void {
		$tasks = $this->getTasks($path, $content);

		foreach ($tasks as $task) {
			if (!$task->shouldRun($path)) {
				continue;
			}

			$task->annotate($path);
		}
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @return array<\IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface>
	 */
	protected function getTasks(string $path, string $content): array {
		$taskCollection = new CallbackAnnotatorTaskCollection();

		return $taskCollection->tasks($this->_io, $this->_config, $path, $content);
	}

}
