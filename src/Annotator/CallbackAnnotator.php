<?php

namespace IdeHelper\Annotator;

class CallbackAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);

		$this->_invokeTasks($path, $content);

		return true;
	}

	/**
	 * @param string $path
	 * @param string $content
	 *
	 * @return void
	 */
	protected function _invokeTasks($path, $content) {
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
	 * @return \IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface[]
	 */
	protected function getTasks($path, $content) {
		$taskCollection = new CallbackAnnotatorTaskCollection();

		return $taskCollection->tasks($this->_io, $this->_config, $path, $content);
	}

}
