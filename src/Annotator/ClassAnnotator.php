<?php
namespace IdeHelper\Annotator;

class ClassAnnotator extends AbstractAnnotator {

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
		$tasks = $this->getTasks($content);

		foreach ($tasks as $task) {
			if (!$task->shouldRun($path, $content)) {
				continue;
			}

			$task->annotate($path);
		}
	}

	/**
	 * @param string $content
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface[]
	 */
	protected function getTasks($content) {
		$taskCollection = new ClassAnnotatorTaskCollection();

		return $taskCollection->tasks($this->_io, $this->_config, $content);
	}

}
