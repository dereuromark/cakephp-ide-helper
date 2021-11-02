<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\CallbackAnnotator;
use IdeHelper\Console\Io;
use RuntimeException;
use Shim\TestSuite\ConsoleOutput;

class CallbackAnnotatorTest extends TestCase {

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

		$path = APP . 'Model/Table/CallbacksTable.php';
		$execPath = TMP . 'CallbacksTable.php';
		copy($path, $execPath);

		$annotator->annotate($execPath);

		$content = file_get_contents($execPath);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$testPath = TEST_FILES . 'Model/Table/CallbacksTable.php';
		$expectedContent = file_get_contents($testPath);
		if ($expectedContent === false) {
			throw new RuntimeException('Cannot read file');
		}
		$this->assertTextEquals($expectedContent, $content);

		$output = $this->out->output();

		$this->assertTextContains('  -> 2 annotations updated.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\CallbackAnnotator
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new CallbackAnnotator($this->io, $params);
	}

}
