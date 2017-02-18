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
			->setMethods(['in', '_stop', '_storeFile'])
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
		$expected = <<<TXT
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\BarBarsTable \$BarBars
 */
class BarController extends AppController {

	/**
	 * @var string
	 */
	public \$modelClass = 'BarBars';

	/**
	 * @var array
	 */
	public \$components = ['Flash'];

}

TXT;
		$this->Shell->expects($this->at(0))->method('_storeFile')->with(
			$this->equalTo(APP . 'Controller' . DS . 'BarController.php'),
			$this->equalTo($expected)
		);

		$expected = <<<TXT
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\FooTable \$Foo
 */
class FooController extends AppController {
}

TXT;
		$this->Shell->expects($this->at(1))->method('_storeFile')->with(
			$this->equalTo(APP . 'Controller' . DS . 'FooController.php'),
			$this->equalTo($expected)
		);

		$this->Shell->runCommand(['controllers', '-v']);
		$output = (string)$this->out->output();

		$this->assertTextContains('BarController', $output);
		$this->assertTextContains('FooController', $output);
	}

}
