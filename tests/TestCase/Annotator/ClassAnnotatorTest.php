<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ClassAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;

class ClassAnnotatorTest extends TestCase {

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

		$path = APP . 'Custom/CustomClass.php';
		$execPath = TMP . 'CustomClass.php';
		copy($path, $execPath);

		$annotator->annotate($execPath);

		$content = file_get_contents($execPath);

		$testPath = TEST_FILES . 'Custom/CustomClass.php';
		$expectedContent = file_get_contents($testPath);
		$this->assertTextEquals($expectedContent, $content);

		$output = $this->out->output();

		$this->assertTextContains('  -> 1 annotation added.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ClassAnnotator
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			//AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new ClassAnnotator($this->io, $params);
	}

}
