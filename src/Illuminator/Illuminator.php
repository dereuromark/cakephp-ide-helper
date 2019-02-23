<?php
namespace IdeHelper\Illuminator;

use Cake\Filesystem\Folder;

class Illuminator {

	/**
	 * @var \IdeHelper\Illuminator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @param \IdeHelper\Illuminator\TaskCollection $taskCollection
	 */
	public function __construct(TaskCollection $taskCollection) {
		$this->taskCollection = $taskCollection;
	}

	/**
	 * @param string $path
	 * @return int
	 */
	public function illuminate($path) {
		$files = $this->getFiles($path);

		$count = 0;
		foreach ($files as $file) {
			if (!$this->taskCollection->run($file)) {
				continue;
			}

			$count++;
		}

		return $count;
	}

	/**
	 * @param string $path
	 * @return array
	 */
	protected function getFiles($path) {
		$folder = new Folder($path);
		$result = $folder->findRecursive('.*\.php', true);

		return $result;
	}

}
