<?php

namespace IdeHelper\Test\TestCase\Console;

use Cake\Console\ConsoleIo;
use Cake\Console\Exception\StopException;
use Cake\TestSuite\TestCase;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class IoTest extends TestCase {

	protected Io $io;

	protected ConsoleIo $consoleIo;

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	/**
	 * @return void
	 */
	protected function setUp(): void {
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
	protected function tearDown(): void {
		parent::tearDown();
		unset($this->Shell);
	}

	/**
	 * @return void
	 */
	public function testSuccess() {
		$this->io->success('Foos');

		$output = $this->out->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testInfo() {
		$this->io->info('Foos');

		$output = $this->out->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testComment() {
		$this->io->comment('Foos');

		$output = $this->out->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testWarn() {
		$this->io->warn('Foos');

		$output = $this->err->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testError() {
		$this->io->error('Foos');

		$output = $this->err->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testErr() {
		$this->io->err('Foos');

		$output = $this->err->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testVerbose() {
		$this->consoleIo->level(ConsoleIo::VERBOSE);
		$this->io->verbose('Foos');

		$output = $this->out->output();
		$this->assertTextContains('Foos', $output);
	}

	/**
	 * @return void
	 */
	public function testQuiet() {
		$this->consoleIo->level(ConsoleIo::QUIET);
		$this->io->quiet('Foos');

		$output = $this->out->output();
		$this->assertTextContains('Foos', $output);
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
	 * @return void
	 */
	public function testAbort() {
		$this->expectException(StopException::class);

		$this->io->abort('Foos');
	}

}
