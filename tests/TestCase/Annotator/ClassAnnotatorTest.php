<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ClassAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class ClassAnnotatorTest extends TestCase {

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

		$file = TMP . 'src' . DS . 'CustomClass.php';
		if (file_exists($file)) {
			unlink($file);
			rmdir(TMP . 'src' . DS);
		}
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();

		$file = TMP . 'src' . DS . 'CustomClass.php';
		if (file_exists($file)) {
			unlink($file);
			rmdir(TMP . 'src' . DS);
		}
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$annotator = $this->_getAnnotatorMock([]);

		$path = APP . 'Custom' . DS . 'CustomClass.php';
		if (!is_dir(TMP . 'src')) {
			mkdir(TMP . 'src', 0770, true);
		}
		$execPath = TMP . 'src' . DS . 'CustomClass.php';
		copy($path, $execPath);

		$annotator->annotate($execPath);

		$content = file_get_contents($execPath);

		$testPath = TEST_FILES . 'Custom' . DS . 'CustomClass.php';
		$expectedContent = file_get_contents($testPath);
		$this->assertTextEquals($expectedContent, $content);

		$output = $this->out->output();

		$this->assertTextContains('  -> 1 annotation added, 1 annotation removed.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ClassAnnotator
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new ClassAnnotator($this->io, $params);
	}

}
