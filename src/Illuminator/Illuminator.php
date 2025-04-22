<?php

namespace IdeHelper\Illuminator;

use IdeHelper\Filesystem\Folder;

class Illuminator {

	protected TaskCollection $taskCollection;

	/**
	 * @param \IdeHelper\Illuminator\TaskCollection $taskCollection
	 */
	public function __construct(TaskCollection $taskCollection) {
		$this->taskCollection = $taskCollection;
	}

	/**
	 * @param string $path
	 * @param string|null $filter
	 * @return int
	 */
	public function illuminate($path, $filter) {
		$files = $this->getFiles($path);

		$count = 0;
		foreach ($files as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			if ($this->shouldSkip($name, $filter)) {
				continue;
			}

			if (!$this->taskCollection->run($file)) {
				continue;
			}

			$count++;
		}

		return $count;
	}

	/**
	 * @param string $fileName
	 * @param string|null $filter
	 *
	 * @return bool
	 */
	protected function shouldSkip($fileName, $filter) {
		if (!$filter) {
			return false;
		}

		return !preg_match('/' . preg_quote($filter, '/') . '/i', $fileName);
	}

	/**
	 * @param string $path
	 * @return array<string>
	 */
	protected function getFiles($path) {
		$folder = new Folder($path);
		$result = $folder->findRecursive('.*\.php', true);

		return $result;
	}

}
