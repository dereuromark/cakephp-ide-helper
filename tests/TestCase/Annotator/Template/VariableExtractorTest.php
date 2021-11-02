<?php

namespace IdeHelper\Test\TestCase\Annotator\Template;

use IdeHelper\Annotator\Template\VariableExtractor;
use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Config;
use Shim\TestSuite\TestCase;

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
	protected function setUp(): void {
		parent::setUp();

		$this->variableExtractor = new VariableExtractor();
	}

	/**
	 * @return void
	 */
	public function testExtract() {
		$path = TEST_ROOT . 'templates' . DS . 'Foos' . DS . 'vars.php';

		$file = $this->getFile($path);

		$result = $this->variableExtractor->extract($file);

		$expected = [
			'obj' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'car' => [
				'type' => 'object',
				'excludeReason' => 'Declared in loop',
			],
			'allCars' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'wheel' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'date' => [
				'type' => 'object',
				'excludeReason' => null,
			],
			'i' => [
				'type' => null,
				'excludeReason' => 'Declared in loop',
			],
			'engine' => [
				'type' => null,
				'excludeReason' => 'Declared in loop',
			],
		];
		foreach ($expected as $name => $data) {
			$this->assertSame($name, $result[$name]['name'], print_r($result[$name], true));
			$this->assertSame($data['type'], $result[$name]['type'], print_r($result[$name], true));
			$this->assertSame($data['excludeReason'], $result[$name]['excludeReason'], print_r($result[$name], true));
		}
	}

	/**
	 * @return void
	 */
	public function testExtractExceptions() {
		$content = <<<CODE
<?php
foreach (\$exceptions as \$exception) {}

try {
} catch (Exception \$e) {
}
CODE;

		$file = $this->getFile('', $content);

		$result = $this->variableExtractor->extract($file);

		$expected = [
			'exceptions' => [
				'excludeReason' => null,
			],
			'exception' => [
				'excludeReason' => 'Declared in loop',
			],
			'e' => [
				'excludeReason' => 'Try catch',
			],
		];
		foreach ($expected as $name => $data) {
			$this->assertSame($data['excludeReason'], $result[$name]['excludeReason'], print_r($result[$name], true));
		}
	}

	/**
	 * @return void
	 */
	public function testExtractAssignment() {
		$content = <<<CODE
<?php
if (strpos(\$module, '.')) {
    [\$prefix, \$moduleName, \$suffix] = explode('.', \$module);
}
//list(\$x, \$y) = [\$z, \$z]; // We dont support the old syntax yet/anymore
CODE;

		$file = $this->getFile('', $content);

		$result = $this->variableExtractor->extract($file);

		$expected = [
			'module' => [
				'excludeReason' => null,
			],
			'moduleName' => [
				'excludeReason' => 'Assignment',
			],
			'prefix' => [
				'excludeReason' => 'Assignment',
			],
			'suffix' => [
				'excludeReason' => 'Assignment',
			],
			/*
			'x' => [
				'excludeReason' => 'Assignment',
			],
			'y' => [
				'excludeReason' => 'Assignment',
			],
			'z' => [
				'excludeReason' => null,
			],
			*/
		];
		foreach ($expected as $name => $data) {
			$this->assertSame($data['excludeReason'], $result[$name]['excludeReason'], print_r($result[$name], true));
		}
	}

	/**
	 * @return void
	 */
	public function testExtractTypeStringAndArray() {
		$content = <<<CODE
<?php
echo \$x['foo'];
echo \$string;
echo \$y . 'z' . \$z;
?>
<?= \$str ?>
CODE;

		$file = $this->getFile('', $content);

		$result = $this->variableExtractor->extract($file);

		$expected = [
			'string' => [
				'type' => 'string',
			],
			'x' => [
				'type' => 'array',
			],
			'y' => [
				'type' => 'string',
			],
			'z' => [
				'type' => 'string',
			],
			'str' => [
				'type' => 'string',
			],
		];
		foreach ($expected as $name => $data) {
			$this->assertSame($data['type'], $result[$name]['type'], print_r($result[$name], true));
		}
	}

}
