<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Console\Io;
use Tools\TestSuite\ConsoleOutput;
use Cake\TestSuite\TestCase;

class ControllerAnnotatorTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

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

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Controller/FooController.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/FooController.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithCustomModelAndLoadModel() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Controller/BarController.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/BarController.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 4 annotations added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithAppController() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Controller/AppController.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/AppController.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 1 annotation added, 1 annotation updated.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ControllerAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true
		];
		return $this->getMockBuilder(ControllerAnnotator::class)->setMethods(['_storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
