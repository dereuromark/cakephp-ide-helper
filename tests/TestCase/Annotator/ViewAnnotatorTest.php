<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ViewAnnotator;
use IdeHelper\Console\Io;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

/**
 */
class ViewAnnotatorTest extends TestCase {

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
	 * @return void
	 */
	public function setUp() {
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
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'View/AppView.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 2 annotations added', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ViewAnnotator|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [AbstractAnnotator::CONFIG_DRY_RUN => true];
		return $this->getMockBuilder(ViewAnnotator::class)->setMethods(['_storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
