<?php

namespace IdeHelper\Test\TestCase\Command\Annotate\Fixture;

use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\PathAwareClassAnnotatorTaskInterface;

/**
 * Second test fixture: declares the same scan path as TestPathAwareAnnotatorTask
 * to exercise the dedup branch in ClassesCommand::_walkPathAwareTasks().
 */
class SecondTestPathAwareAnnotatorTask extends AbstractClassAnnotatorTask implements PathAwareClassAnnotatorTaskInterface {

	/**
	 * @return array<string>
	 */
	public static function scanPaths(): array {
		return ['tests/fixtures-pathaware/'];
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
