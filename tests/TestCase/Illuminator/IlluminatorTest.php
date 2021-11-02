<?php

namespace IdeHelper\Test\TestCase\Illuminator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
use Shim\TestSuite\ConsoleOutput;

class IlluminatorTest extends TestCase {

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $io;

	/**
	 * @var \IdeHelper\Illuminator\Illuminator
	 */
	protected $illuminator;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$consoleIo->level($consoleIo::VERBOSE);
		$this->io = new Io($consoleIo);

		$taskCollection = new TaskCollection($this->io, ['dry-run' => true]);

		$this->illuminator = new Illuminator($taskCollection);
	}

	/**
	 * @return void
	 */
	public function testIlluminate() {
		$path = TEST_FILES;
		$count = $this->illuminator->illuminate($path, null);

		$this->assertSame(10, $count);

		$out = $this->out->output();

		$this->assertTextContains('public const FIELD_ID = \'id\';', $out);
	}

}
