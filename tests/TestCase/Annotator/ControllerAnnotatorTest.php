<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ControllerAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class ControllerAnnotatorTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var array
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

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
	 * @return void
	 */
	protected function setUp(): void {
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
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/FooController.php';
		$annotator->annotate($path);

		$output = $this->out->output();

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
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/BarController.php';
		$annotator->annotate($path);

		$output = $this->out->output();

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
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Controller/AppController.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ControllerAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(ControllerAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithPluginController() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Controller/HousesController.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . '/plugins/Controllers/src/Controller/HousesController.php';
		$annotator->setConfig(ControllerAnnotator::CONFIG_PLUGIN, 'Controllers');
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithPluginControllerExplicit() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Controller/WindowsController.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . '/plugins/Controllers/src/Controller/WindowsController.php';
		$annotator->setConfig(ControllerAnnotator::CONFIG_PLUGIN, 'Controllers');
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithPluginControllerNoModel() {
		$annotator = $this->_getAnnotatorMock([]);

		$annotator->expects($this->never())
			->method('storeFile');

		$path = TEST_ROOT . '/plugins/Controllers/src/Controller/GenericController.php';
		$annotator->setConfig(ControllerAnnotator::CONFIG_PLUGIN, 'Awesome');
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextNotContains('annotation added.', $output);
	}

}
