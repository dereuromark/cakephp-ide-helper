<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\CallbackAnnotator;
use IdeHelper\Console\Io;
use Tools\TestSuite\ConsoleOutput;
use Cake\TestSuite\TestCase;

class CallbackAnnotatorTest extends TestCase {

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
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

		$path = APP . 'Model/Table/CallbacksTable.php';
		$execPath = TMP . 'CallbacksTable.php';
		copy($path, $execPath);

		$annotator->annotate($execPath);

		$content = file_get_contents($execPath);

		$testPath = TEST_FILES . 'Model/Table/CallbacksTable.php';
		$expectedContent = file_get_contents($testPath);
		$this->assertTextEquals($expectedContent, $content);

		$output = (string)$this->out->output();

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
