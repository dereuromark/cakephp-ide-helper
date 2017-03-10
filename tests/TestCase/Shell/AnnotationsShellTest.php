<?php

namespace IdeHelper\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use IdeHelper\Shell\AnnotationsShell;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

/**
 */
class AnnotationsShellTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		//'plugin.ide_helper.foo',
		//'plugin.ide_helper.bar_bars',
		'plugin.ide_helper.cars',
		'plugin.ide_helper.wheels',
	];

	/**
	 * @var \IdeHelper\Shell\AnnotationsShell|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $Shell;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $out;

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

		$this->Shell = $this->getMockBuilder(AnnotationsShell::class)
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
	public function _testModels() {
		$this->Shell->runCommand(['models', '-d', '-v']);

		$output = $this->out->output();
		$this->assertTextContains('* 2 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testView() {
		$this->Shell->runCommand(['view', '-d', '-v']);

		$output = $this->out->output();
		$this->assertTextContains('* 2 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testTemplates() {
		$this->Shell->runCommand(['templates', '-d', '-v']);

		$output = $this->out->output();
		$this->assertTextContains('* 2 annotations added', $output);
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
