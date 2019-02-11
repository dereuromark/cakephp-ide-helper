<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Illuminator;
use IdeHelper\Illuminator\TaskCollection;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class IlluminatorTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
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
	public function setUp() {
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
		$count = $this->illuminator->illuminate($path);

		$this->assertSame(5, $count);

		$out = $this->out->output();
		$this->assertTextContains('public const FIELD_ID = \'id\';', $out);
	}

}
