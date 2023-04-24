<?php

namespace IdeHelper\Test\TestCase\Command;

use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use IdeHelper\Command\GeneratePhpStormMetaCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Shim\TestSuite\ConsoleOutput;

class GeneratePhpstormMetaCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected GeneratePhpStormMetaCommand|MockObject $command;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected ConsoleOutput $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected ConsoleOutput $err;

	protected ConsoleIo $io;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}
		if (file_exists(TMP . 'phpstorm' . DS . '.meta.php')) {
			unlink(TMP . 'phpstorm' . DS . '.meta.php');
		}
		if (is_dir(TMP . 'phpstorm')) {
			rmdir(TMP . 'phpstorm');
		}

		$this->command = $this->getMockBuilder(GeneratePhpStormMetaCommand::class)
			->onlyMethods(['getMetaFilePath'])
			->getMock();
		$this->command->expects($this->any())->method('getMetaFilePath')->willReturn(TMP . 'phpstorm' . DS . '.meta.php');
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();
		unset($this->command);
	}

	/**
	 * @return void
	 */
	public function testDirExists() {
		$this->assertFalse(file_exists(TMP . 'phpstorm'));

		//$this->exec('phpstorm');
		//$this->command->executeCommand(GeneratePhpStormMetaCommand::class, [], $this->io);
		$this->assertTrue(file_exists(TMP . 'phpstorm' . DS . '.meta.php'));
	}

	/**
	 * @return void
	 */
	public function testDirExistsDryRun() {
		$this->assertFalse(file_exists(TMP . 'phpstorm'));
		//$this->exec('phpstorm -d');

		$this->assertFalse(file_exists(TMP . 'phpstorm' . DS . '.meta.php'));
		$this->assertFalse(file_exists(TMP . 'phpstorm'));
	}

	/**
	 * @return void
	 */
	public function testGenerateDryRun() {
		//$this->exec('phpstorm -d');

		$output = $this->_out->output();
		$this->assertTextContains(' needs updating', $output);

		$this->assertSame(GeneratePhpStormMetaCommand::CODE_CHANGES, $this->_exitCode);
	}

	/**
	 * @return void
	 */
	public function testGenerate() {
		//$this->exec('phpstorm');

		$output = $this->_out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.', $output);

		//$this->exec('phpstorm');

		$output = $this->_out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.', $output);

		$this->assertSame(GeneratePhpStormMetaCommand::CODE_SUCCESS, $this->_exitCode);
	}

}
