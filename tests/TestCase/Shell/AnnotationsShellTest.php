<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Shell\AnnotationsShell;
use Tools\TestSuite\ConsoleOutput;

class AnnotationsShellTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Shell\AnnotationsShell|\PHPUnit\Framework\MockObject\MockObject
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
	public function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(AnnotationsShell::class)
			->setMethods(['in', '_stop', '_storeFile'])
			->setConstructorArgs([$io])
			->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
		unset($this->Shell);
	}

	/**
	 * @return void
	 */
	public function testModels() {
		$this->Shell->runCommand(['models', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testView() {
		$this->Shell->runCommand(['view', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testHelpers() {
		$this->Shell->runCommand(['helpers', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testComponents() {
		$this->Shell->runCommand(['components', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testShells() {
		$this->Shell->runCommand(['shells', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testTemplates() {
		$this->Shell->runCommand(['templates', '-d', '-v', '-r']);

		$output = $this->out->output();
		$this->assertTextContains(' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testControllers() {
		$this->Shell->runCommand(['controllers', '-d', '-v', '-r']);
		$output = (string)$this->out->output();

		$this->assertTextContains('BarController', $output);
		$this->assertTextContains(' annotations added', $output);
		$this->assertTextContains('FooController', $output);
	}

	/**
	 * @return void
	 */
	public function testAll() {
		$result = $this->Shell->runCommand(['all', '-d', '-v', '-r']);
		$this->assertSame(AnnotationsShell::CODE_SUCCESS, $result);

		$output = (string)$this->out->output();

		$this->assertTextContains('[Models]', $output);
		$this->assertTextContains('[Controllers]', $output);
		$this->assertTextContains('[View]', $output);
		$this->assertTextContains('[Templates]', $output);
		$this->assertTextContains('[Shells]', $output);
		$this->assertTextContains('[Components]', $output);
		$this->assertTextContains('[Helpers]', $output);
	}

	/**
	 * @return void
	 */
	public function testAllCiMode() {
		$result = $this->Shell->runCommand(['all', '-d', '-v', '--ci', '-p', 'Awesome']);

		$this->assertSame(AnnotationsShell::CODE_SUCCESS, $result);
	}

	/**
	 * @return void
	 */
	public function testAllCiModeChanges() {
		$result = $this->Shell->runCommand(['all', '-d', '-v', '--ci']);

		$this->assertSame(AnnotationsShell::CODE_CHANGES, $result);
	}

	/**
	 * @return void
	 */
	public function testClasses() {
		$result = $this->Shell->runCommand(['classes', '-d', '-v']);

		$this->assertNull($result);
	}

	/**
	 * @return void
	 */
	public function testCallbacks() {
		$result = $this->Shell->runCommand(['callbacks', '-d', '-v']);

		$this->assertNull($result);
	}

}
