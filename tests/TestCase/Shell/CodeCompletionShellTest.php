<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Shell\CodeCompletionShell;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class CodeCompletionShellTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.ide_helper.cars',
		'plugin.ide_helper.wheels',
	];

	/**
	 * @var \IdeHelper\Shell\CodeCompletionShell|\PHPUnit\Framework\MockObject\MockObject
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
		if (file_exists(TMP . 'phpstorm' . DS . '.meta.php')) {
			unlink(TMP . 'phpstorm' . DS . '.meta.php');
			rmdir(TMP . 'phpstorm');
		}

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(CodeCompletionShell::class)
			->setMethods(['_stop', 'getMetaFilePath'])
			->setConstructorArgs([$io])
			->getMock();
		$this->Shell->expects($this->any())->method('getMetaFilePath')->willReturn(TMP . 'phpstorm' . DS . '.meta.php');
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
	public function testGenerate() {
		$result = $this->Shell->runCommand(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('CodeCompletion files generated: Cake\ORM', $output);

		$this->assertSame(CodeCompletionShell::CODE_SUCCESS, $result);
	}

}
