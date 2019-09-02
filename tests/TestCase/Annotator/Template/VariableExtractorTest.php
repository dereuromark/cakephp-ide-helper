<?php

namespace IdeHelper\Test\TestCase\Annotator\Template;

use IdeHelper\Annotator\Template\VariableExtractor;
use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Config;
use Tools\TestSuite\TestCase;

$composerVendorDir = ROOT . DS . 'vendor';
$codesnifferDir = 'squizlabs' . DS . 'php_codesniffer';
$manualAutoload = $composerVendorDir . DS . $codesnifferDir . DS . 'autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

/**
 * @property \IdeHelper\Annotator\Template\VariableExtractor $variableExtractor
 */
class VariableExtractorTest extends TestCase {

	use FileTrait;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->variableExtractor = new VariableExtractor();
	}

	/**
	 * @return void
	 */
	public function testExtract() {
		$path = TEST_ROOT . 'src' . DS . 'Template' . DS . 'Foos' . DS . 'vars.ctp';

		$file = $this->_getFile($path);

		$result = $this->variableExtractor->extract($file);

		$expected = [
			'obj' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'car' => [
				'type' => null,
				'excludeReason' => 'Declared in loop',
			],
			'finalCar' => [
				'type' => 'string',
				'excludeReason' => 'Assignment',
			],
			'wheel' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'date' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'cars' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'i' => [
				'type' => null,
				'excludeReason' => 'Declared in loop',
			],
			'engine' => [
				'name' => 'engine',
				'type' => null,
				'excludeReason' => null,
			],
		];
		foreach ($expected as $name => $data) {
			$this->assertSame($name, $result[$name]['name'], print_r($result[$name], true));
			$this->assertSame($data['type'], $result[$name]['type'], print_r($result[$name], true));
			$this->assertSame($data['excludeReason'], $result[$name]['excludeReason'], print_r($result[$name], true));
		}
	}

}
