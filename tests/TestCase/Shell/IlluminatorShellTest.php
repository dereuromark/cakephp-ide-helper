<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Shell\IlluminatorShell;
use PHPUnit\Framework\MockObject\MockObject;
use Shim\TestSuite\ConsoleOutput;

class IlluminatorShellTest extends TestCase {

	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected IlluminatorShell|MockObject $Shell;

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

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(IlluminatorShell::class)
			->setMethods(['_stop'])
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
		$result = $this->Shell->run(['illuminate', '-d', '-v']);

		$output = $this->out->output();
		$this->assertTextContains('# /src/Illuminator/Illuminator.php', $output);

		$this->assertSame(IlluminatorShell::CODE_SUCCESS, $result);
	}

}
