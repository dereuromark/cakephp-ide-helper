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
		$content = file_get_contents($path);

		$result = $this->variableExtractor->extract($content);

		//TODO/FIXME
		$expected = [
			'obj' => [
				'name' => 'obj'
			],
			'allCars' => [
				'name' => 'allCars'
			],
			'car' => [
				'name' => 'car'
			],
			'finalCarTime' => [
				'name' => 'finalCarTime'
			],
			'wheel' => [
				'name' => 'wheel'
			],
			'date' => [
				'name' => 'date'
			],
			'i' => [
				'name' => 'i'
			],
			'engine' => [
				'name' => 'engine'
			],
			'foos' => [
				'name' => 'foos'
			],
			'foo' => [
				'name' => 'foo'
			]
		];
		debug($result);
		$this->assertEquals($expected, $result);
	}
}
