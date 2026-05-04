<?php

namespace IdeHelper\Test\TestCase\Command\Annotate\Fixture;

use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\PathAwareClassAnnotatorTaskInterface;

/**
 * Test fixture: a class annotator task that declares a custom scan path
 * via PathAwareClassAnnotatorTaskInterface but does no real annotation
 * work (shouldRun always false).
 */
class TestPathAwareAnnotatorTask extends AbstractClassAnnotatorTask implements PathAwareClassAnnotatorTaskInterface {

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
