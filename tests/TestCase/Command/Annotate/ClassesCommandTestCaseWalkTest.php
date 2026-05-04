<?php

namespace IdeHelper\Test\TestCase\Command\Annotate;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Regression test for the default `tests/TestCase/` walk performed by
 * `bin/cake annotate classes` when `TestClassAnnotatorTask` is in the
 * registered task list. Guards against the walk silently disappearing
 * during refactors.
 */
class ClassesCommandTestCaseWalkTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @var array<string>
	 */
	protected array $createdFiles = [];

	/**
	 * @var array<string>
	 */
	protected array $createdDirs = [];

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['IdeHelper']);
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		foreach ($this->createdFiles as $path) {
			@unlink($path);
		}
		foreach (array_reverse($this->createdDirs) as $dir) {
			@rmdir($dir);
		}
		$this->createdFiles = [];
		$this->createdDirs = [];
		parent::tearDown();
	}

	/**
	 * Drop a controller-test class into the app's tests/TestCase/Controller/
	 * tree so the command's verbose output reports the directory walk.
	 *
	 * @return void
	 */
	protected function placeControllerTestFile(): void {
		$testCaseDir = ROOT . DS . 'tests' . DS . 'TestCase' . DS;
		$controllerDir = $testCaseDir . 'Controller' . DS;
		foreach ([$testCaseDir, $controllerDir] as $dir) {
			if (!is_dir($dir)) {
				mkdir($dir, 0o777, true);
				$this->createdDirs[] = $dir;
			}
		}
		$path = $controllerDir . 'WalkProbeControllerTest.php';
		file_put_contents(
			$path,
			"<?php\nnamespace App\\Test\\TestCase\\Controller;\nclass WalkProbeControllerTest {}\n",
		);
		$this->createdFiles[] = $path;
	}

	/**
	 * The default test-case scan must reach files inside tests/TestCase/.
	 * Verbose output emits the directory header for every walked folder, so
	 * we assert the marker shows up.
	 *
	 * @return void
	 */
	public function testTestCaseDirectoryIsWalkedByDefault(): void {
		$this->placeControllerTestFile();

		$this->exec('annotate classes -d -v');
		$this->assertExitSuccess();
		$this->assertOutputContains('tests' . DS . 'TestCase' . DS . 'Controller');
		$this->assertOutputContains('WalkProbeControllerTest');
	}

}
