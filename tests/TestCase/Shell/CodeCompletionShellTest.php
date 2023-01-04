<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Shell\CodeCompletionShell;
use PHPUnit\Framework\MockObject\MockObject;
use Shim\TestSuite\ConsoleOutput;

class CodeCompletionShellTest extends TestCase {

	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected CodeCompletionShell|MockObject $Shell;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $err;

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
			rmdir(TMP . 'phpstorm');
		}

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(CodeCompletionShell::class)
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
	public function testGenerate() {
		$result = $this->Shell->run(['generate']);

		$output = $this->out->output();
		$this->assertTextContains('CodeCompletion files generated: Cake\Controller, Cake\ORM, Cake\View', $output);

		$this->assertSame(CodeCompletionShell::CODE_SUCCESS, $result);
	}

}
