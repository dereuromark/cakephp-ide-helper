<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Shell\IlluminatorShell;
use Shim\TestSuite\ConsoleOutput;

class IlluminatorShellTest extends TestCase {

	/**
	 * @var array
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Shell\IlluminatorShell|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected $Shell;

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

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(IlluminatorShell::class)
			->setMethods(['_stop'])
			->setConstructorArgs([$io])
			->getMock();
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
	public function testIlluminateDryRun() {
		$result = $this->Shell->runCommand(['illuminate', '-d', '-v']);

		$output = $this->out->output();
		$this->assertTextContains('# /src/Illuminator/Illuminator.php', $output);

		$this->assertSame(IlluminatorShell::CODE_SUCCESS, $result);
	}

}
