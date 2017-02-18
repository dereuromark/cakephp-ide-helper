<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Shell\AnnotationShell;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

/**
 */
class AnnotationShellTest extends TestCase {

	/**
	 * @var \IdeHelper\Shell\AnnotationShell|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $Shell;

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
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(AnnotationShell::class)
			->setMethods(['in', '_stop'])
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
	public function testControllers() {
		$this->Shell->runCommand(['controllers']);
		$output = (string)$this->out->output();

		debug($output);
	}

}
