<?php

namespace IdeHelper\Test\TestCase\Command\Annotate\Fixture;

use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\PathAwareClassAnnotatorTaskInterface;

/**
 * Second test fixture: declares the same scan path as TestPathAwareAnnotatorTask
 * but in a deliberately different shape — backslash separators and no trailing
 * slash — to exercise both the dedup branch in
 * ClassesCommand::_walkPathAwareTasks() and the path normalization that makes
 * the dedup key stable across separator / trailing-slash style variations.
 */
class SecondTestPathAwareAnnotatorTask extends AbstractClassAnnotatorTask implements PathAwareClassAnnotatorTaskInterface {

	/**
	 * @return array<string>
	 */
	public static function scanPaths(): array {
		return ['tests\\fixtures-pathaware'];
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		return false;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		return false;
	}

}
