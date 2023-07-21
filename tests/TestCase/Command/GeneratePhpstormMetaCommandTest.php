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

	protected const META_FOLDER = ROOT . DS . '.phpstorm.meta.php' . DS;
	protected const META_FILE = self::META_FOLDER . '.ide-helper.meta.php';

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}
		if (file_exists(static::META_FILE)) {
			unlink(static::META_FILE);
		}
		if (is_dir(static::META_FOLDER)) {
			rmdir(static::META_FOLDER);
		}
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
		$this->assertFalse(file_exists(static::META_FILE));

		$this->exec('generate phpstorm');
		$this->assertTrue(file_exists(static::META_FILE));
	}

	/**
	 * @return void
	 */
	public function testDirExistsDryRun() {
		$this->assertFalse(file_exists(static::META_FILE));
		$this->exec('generate phpstorm -d');

		$this->assertFalse(file_exists(static::META_FILE));
		$this->assertFalse(file_exists(static::META_FOLDER));
	}

	/**
	 * @return void
	 */
	public function testGenerateDryRun() {
		$this->exec('generate phpstorm -d');
		$this->assertOutputContains(' needs updating');

		$this->assertSame(GeneratePhpStormMetaCommand::CODE_CHANGES, $this->_exitCode);
	}

	/**
	 * @return void
	 */
	public function testGenerate() {
		$this->exec('generate phpstorm');
		$this->assertOutputContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.');

		$this->exec('generate phpstorm');
		$this->assertOutputContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.');

		$this->assertExitSuccess();
	}

}
