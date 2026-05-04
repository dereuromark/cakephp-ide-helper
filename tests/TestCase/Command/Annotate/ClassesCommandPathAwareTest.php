<?php

namespace IdeHelper\Test\TestCase\Command\Annotate;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Test\TestCase\Command\Annotate\Fixture\SecondTestPathAwareAnnotatorTask;
use IdeHelper\Test\TestCase\Command\Annotate\Fixture\TestPathAwareAnnotatorTask;

class ClassesCommandPathAwareTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @var array<string>
	 */
	protected array $createdFiles = [];

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['IdeHelper', 'Awesome']);
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		Configure::delete('IdeHelper.classAnnotatorTasks');
		foreach ($this->createdFiles as $path) {
			@unlink($path);
		}
		$this->createdFiles = [];
		@rmdir(ROOT . DS . 'tests' . DS . 'fixtures-pathaware');
		@rmdir(ROOT . DS . 'tests' . DS);
		@rmdir(PLUGINS . 'Awesome' . DS . 'tests' . DS . 'fixtures-pathaware');
		@rmdir(PLUGINS . 'Awesome' . DS . 'tests');
		parent::tearDown();
	}

	/**
	 * @return string
	 */
	protected function placePluginFixtureFile(): string {
		$dir = PLUGINS . 'Awesome' . DS . 'tests' . DS . 'fixtures-pathaware' . DS;
		if (!is_dir($dir)) {
			mkdir($dir, 0o777, true);
		}
		$path = $dir . 'PluginScannedClass.php';
		file_put_contents($path, "<?php\nclass PluginScannedClass {}\n");
		$this->createdFiles[] = $path;

		return $path;
	}

	/**
	 * Drop a tiny PHP class into the directory the fixture task declares so
	 * `_classes()` will visit it and emit verbose-mode output we can match.
	 *
	 * @return string
	 */
	protected function placeFixtureFile(): string {
		$dir = ROOT . DS . 'tests' . DS . 'fixtures-pathaware' . DS;
		if (!is_dir($dir)) {
			mkdir($dir, 0o777, true);
		}
		$path = $dir . 'CustomScannedClass.php';
		file_put_contents($path, "<?php\nclass CustomScannedClass {}\n");
		$this->createdFiles[] = $path;

		return $path;
	}

	/**
	 * @return void
	 */
	public function testPathAwareTaskDirectoryIsWalked(): void {
		$this->placeFixtureFile();

		Configure::write('IdeHelper.classAnnotatorTasks', [
			TestPathAwareAnnotatorTask::class => TestPathAwareAnnotatorTask::class,
		]);

		$this->exec('annotate classes -d -v');
		$this->assertExitSuccess();
		$this->assertOutputContains('tests' . DS . 'fixtures-pathaware');
		$this->assertOutputContains('CustomScannedClass');
	}

	/**
	 * Plugin-mode integration: with `-p PluginName`, scanPaths() is resolved
	 * relative to the plugin's root, so the fixture directory inside the
	 * plugin is reached even though the same path inside the app is empty.
	 *
	 * @return void
	 */
	public function testPathAwareTaskWalksPluginPathInPluginMode(): void {
		$this->placePluginFixtureFile();

		Configure::write('IdeHelper.classAnnotatorTasks', [
			TestPathAwareAnnotatorTask::class => TestPathAwareAnnotatorTask::class,
		]);

		$this->exec('annotate classes -p Awesome -d -v');
		$this->assertExitSuccess();
		$this->assertOutputContains('fixtures-pathaware');
		$this->assertOutputContains('PluginScannedClass');
	}

	/**
	 * Two registered path-aware tasks declaring the same scan path: the
	 * directory must be walked once. Verbose output shows the directory
	 * header on every walk, so we count occurrences and assert exactly 1.
	 *
	 * @return void
	 */
	public function testDuplicateScanPathsAcrossTasksAreWalkedOnlyOnce(): void {
		$this->placeFixtureFile();

		Configure::write('IdeHelper.classAnnotatorTasks', [
			TestPathAwareAnnotatorTask::class => TestPathAwareAnnotatorTask::class,
			SecondTestPathAwareAnnotatorTask::class => SecondTestPathAwareAnnotatorTask::class,
		]);

		$this->exec('annotate classes -d -v');
		$this->assertExitSuccess();

		$marker = 'tests' . DS . 'fixtures-pathaware';
		$count = substr_count($this->_out->messages()[0] ?? '', $marker);
		// Some Cake versions return messages as array; concat for safety:
		if (!$count) {
			$count = substr_count(implode("\n", $this->_out->messages()), $marker);
		}
		$this->assertSame(
			1,
			$count,
			"Directory '{$marker}' should be walked exactly once when two tasks declare it; saw {$count} occurrences.",
		);
	}

	/**
	 * @return void
	 */
	public function testNonExistentPathAwareDirectoryIsSkippedSilently(): void {
		Configure::write('IdeHelper.classAnnotatorTasks', [
			TestPathAwareAnnotatorTask::class => TestPathAwareAnnotatorTask::class,
		]);

		$this->exec('annotate classes -d -v');
		$this->assertExitSuccess();
		// No fixture file placed; the declared directory does not exist.
		// Just must not crash; default scan still ran.
	}

}
