<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ShellAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class ShellAnnotatorTest extends TestCase {

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

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Shell/MyShell.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Shell/MyShell.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 3 annotations added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotatePlugins() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Shell/MyPluginShell.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Shell/MyPluginShell.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 2 annotations added.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ShellAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(ShellAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
