<?php

namespace IdeHelper\Test\TestCase\Command\Annotate;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
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

		$this->loadPlugins(['IdeHelper']);
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
		parent::tearDown();
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
