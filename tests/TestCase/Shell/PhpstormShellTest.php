<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Shell\PhpstormShell;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class PhpstormShellTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.ide_helper.cars',
		'plugin.ide_helper.wheels',
	];

	/**
	 * @var \IdeHelper\Shell\PhpstormShell|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $Shell;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}
		if (file_exists(TMP . '.meta.php')) {
			unlink(TMP . '.meta.php');
		}

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(PhpstormShell::class)
			->setMethods(['_stop', 'getMetaFilePath'])
			->setConstructorArgs([$io])
			->getMock();
		$this->Shell->expects($this->any())->method('getMetaFilePath')->willReturn(TMP . '.meta.php');
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Shell);
	}

	/**
	 * @return void
	 */
	public function testDirExists() {
		$this->assertSame(false, file_exists(TMP . '.meta.php'));
		$this->Shell->runCommand(['generate']);
		$this->assertSame(true, file_exists(TMP . '.meta.php'));
	}

	/**
	 * @return void
	 */
	public function testDirExistsDryRun() {
		$this->assertSame(false, file_exists(TMP . '.meta.php'));
		$this->Shell->runCommand(['generate', '-d']);
		$this->assertSame(false, file_exists(TMP . '.meta.php'));
	}

	/**
	 * @return void
	 */
	public function testGenerateDryRun() {
		$result = $this->Shell->runCommand(['generate', '-d']);

		$output = $this->out->output();
		$this->assertTextContains(' needs updating', $output);

		$this->assertSame(PhpstormShell::CODE_CHANGES, $result);
	}

	/**
	 * @return void
	 */
	public function testGenerate() {
		$result = $this->Shell->runCommand(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` generated.', $output);

		$result = $this->Shell->runCommand(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('Meta file `/.phpstorm.meta.php/.ide-helper.meta.php` still up to date.', $output);

		$this->assertSame(PhpstormShell::CODE_SUCCESS, $result);
	}

}
