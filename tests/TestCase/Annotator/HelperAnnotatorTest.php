<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use Cake\View\Helper;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\HelperAnnotator;
use IdeHelper\Console\Io;
use ReflectionClass;
use Shim\TestSuite\ConsoleOutput;

class HelperAnnotatorTest extends TestCase {

	use DiffHelperTrait;

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected Io $io;

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

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'View/Helper/MyHelper.php'));
		$expectedCount = 4;
		if (!$this->helperBaseSupportsGenerics()) {
			$expectedContent = str_replace(" * @extends \\Cake\\View\\Helper<\\Cake\\View\\View>\n *\n", '', $expectedContent);
			$expectedCount = 3;
		}
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'View/Helper/MyHelper.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> ' . $expectedCount . ' annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotatePreservesCustomMethodAnnotations() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'View/Helper/MyMethodHelper.php'));
		$expectedCount = 3;
		if (!$this->helperBaseSupportsGenerics()) {
			$expectedContent = str_replace(" * @extends \\Cake\\View\\Helper<\\Cake\\View\\View>\n", '', $expectedContent);
			$expectedCount = 2;
		}
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'View/Helper/MyMethodHelper.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> ' . $expectedCount . ' annotations added', $output);
	}

	/**
	 * Whether the base helper declares a template parameter (CakePHP 5.3+) and
	 * therefore receives an `@extends` annotation.
	 *
	 * @return bool
	 */
	protected function helperBaseSupportsGenerics(): bool {
		$doc = (new ReflectionClass(Helper::class))->getDocComment();

		return $doc !== false && preg_match('/^\s*\*\s*@template\s/m', $doc) === 1;
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\HelperAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params): HelperAnnotator {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(HelperAnnotator::class)->onlyMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
