<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Console\Io;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

/**
 */
class IoTest extends TestCase {

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $io;

	/**
	 * @var \Cake\Console\ConsoleIo
	 */
	protected $consoleIo;

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

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$this->consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($this->consoleIo);
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
	public function testSuccess() {
		$this->io->success('Foo');

		$output = $this->out->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testInfo() {
		$this->io->info('Foo');

		$output = $this->out->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testComment() {
		$this->io->comment('Foo');

		$output = $this->out->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testWarn() {
		$this->io->warn('Foo');

		$output = $this->err->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testErr() {
		$this->io->err('Foo');

		$output = $this->err->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testVerbose() {
		$this->consoleIo->level(ConsoleIo::VERBOSE);
		$this->io->verbose('Foo');

		$output = $this->out->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testQuiet() {
		$this->consoleIo->level(ConsoleIo::QUIET);
		$this->io->quiet('Foo');

		$output = $this->out->output();
		$this->assertTextContains('Foo', $output);
	}

	/**
	 * @return void
	 */
	public function testNl() {
		$output = $this->io->nl();

		$this->assertSame(ConsoleOutput::LF, $output);
	}

	/**
	 * @return void
	 */
	public function testHr() {
		$this->io->hr();

		$output = $this->out->output();
		$this->assertTextContains('----', $output);
	}

	/**
	 * @expectedException \Cake\Console\Exception\StopException
	 * @return void
	 */
	public function testAbort() {
		$this->io->abort('Foo');
	}

}
