<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\RoutesAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestTrait;

class RoutesAnnotatorTest extends TestCase {

	use DiffHelperTrait;
	use TestTrait;

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

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'routes/after/empty.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_FILES . 'routes/before/empty.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * Tests merging with existing doc block.
	 *
	 * @return void
	 */
	public function testAnnotateExisting() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'routes/after/existing.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_FILES . 'routes/before/existing.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertSame('', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block and replacing outdated annotations.
	 *
	 * @return void
	 */
	public function testAnnotateExistingOutdated() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'routes/after/outdated.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_FILES . 'routes/before/outdated.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation updated.', $output);
	}

	/**
	 * @return void
	 */
	public function testHasAnnotation() {
		$annotator = $this->_getAnnotatorMock([]);

		$content = <<<'PHP'
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */
PHP;
		$result = $this->invokeMethod($annotator, 'hasAnnotation', [$content]);
		$this->assertTrue($result);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\RoutesAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(RoutesAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
