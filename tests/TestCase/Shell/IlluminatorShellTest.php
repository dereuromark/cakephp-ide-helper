<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Shell\IlluminatorShell;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class IlluminatorShellTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.ide_helper.cars',
		'plugin.ide_helper.wheels',
	];

	/**
	 * @var \IdeHelper\Shell\IlluminatorShell|\PHPUnit\Framework\MockObject\MockObject
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
	public function tearDown() {
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
