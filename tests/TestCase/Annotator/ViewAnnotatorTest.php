<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class ViewAnnotatorTest extends TestCase {

	use DiffHelperTrait;

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

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'View/AppView.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'View/AppView.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 3 annotations added', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ViewAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(ViewAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
