<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Shell\PhpstormShell;
use PHPUnit\Framework\MockObject\MockObject;
use Shim\TestSuite\ConsoleOutput;

class PhpstormShellTest extends TestCase {

	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected PhpstormShell|MockObject $Shell;

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->skipIf(true, 'Deprecated, will be moved to Command');

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}
		if (file_exists(TMP . 'phpstorm' . DS . '.meta.php')) {
			unlink(TMP . 'phpstorm' . DS . '.meta.php');
		}
		if (is_dir(TMP . 'phpstorm')) {
			rmdir(TMP . 'phpstorm');
		}

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(GeneratePhpStormCommand::class)
			->setMethods(['_stop', 'getMetaFilePath'])
			->getMock();
		$this->Shell->expects($this->any())->method('getMetaFilePath')->willReturn(TMP . 'phpstorm' . DS . '.meta.php');
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();
		unset($this->Shell);
	}

	/**
	 * @return void
	 */
	public function testDirExists() {
		$this->assertFalse(file_exists(TMP . 'phpstorm'));
		$this->Shell->run(['generate']);
		$this->assertTrue(file_exists(TMP . 'phpstorm' . DS . '.meta.php'));
	}

	/**
	 * @return void
	 */
	public function testDirExistsDryRun() {
		$this->assertFalse(file_exists(TMP . 'phpstorm'));
		$this->Shell->run(['generate', '-d']);
		$this->assertFalse(file_exists(TMP . 'phpstorm' . DS . '.meta.php'));
		$this->assertFalse(file_exists(TMP . 'phpstorm'));
	}

	/**
	 * @return void
	 */
	public function testGenerateDryRun() {
		$result = $this->Shell->run(['generate', '-d']);

		$output = $this->out->output();
		$this->assertTextContains(' needs updating', $output);

		$this->assertSame(GeneratePhpStormCommand::CODE_CHANGES, $result);
	}

	/**
	 * @return void
	 */
	public function testGenerate() {
		$result = $this->Shell->run(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.', $output);

		$result = $this->Shell->run(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.', $output);

		$this->assertSame(GeneratePhpStormCommand::CODE_SUCCESS, $result);
	}

}
