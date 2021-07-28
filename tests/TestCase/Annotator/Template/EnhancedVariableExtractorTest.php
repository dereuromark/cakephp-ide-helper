<?php

namespace IdeHelper\Test\TestCase\Annotator\Template;

use IdeHelper\Annotator\Template\EnhancedVariableExtractor;
use IdeHelper\Annotator\Traits\FileTrait;
use Shim\TestSuite\TestCase;

/**
 * @property \IdeHelper\Annotator\Template\EnhancedVariableExtractor $variableExtractor
 */
class EnhancedVariableExtractorTest extends TestCase {

	use FileTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->variableExtractor = new EnhancedVariableExtractor();
	}

	/**
	 * @return void
	 */
	public function testExtract() {
		$path = TEST_ROOT . 'templates' . DS . 'Foos' . DS . 'vars.php';

		$result = $this->variableExtractor->extract($path);

		//TODO
	}
}
