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

class VariableExtractorTest extends TestCase {

	use FileTrait;

	protected VariableExtractor $variableExtractor;

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
		$path = APP_ROOT . DS . 'templates' . DS . 'Foos' . DS . 'vars.php';

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
		$content = <<<'PHP'
<?php
foreach ($exceptions as $exception) {}

try {
} catch (Exception $e) {
}
PHP;

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
		$content = <<<'PHP'
<?php
if (strpos($module, '.')) {
    [$prefix, $moduleName, $suffix] = explode('.', $module);
}
//list($x, $y) = [$z, $z]; // We dont support the old syntax yet/anymore
PHP;

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
		$content = <<<'PHP'
<?php
echo $x['foo'];
echo $string;
echo $y . 'z' . $z;
?>
<?= $str ?>
PHP;

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
	/**
	 * @return void
	 */
	public function testExtractFromStrings(): void {
		$content = <<<'PHP'
<?php
echo "<strong>'{$url}'</strong>: $title.";
PHP;
		$file = $this->getFile('', $content);

		$result = $this->variableExtractor->extract($file);

		$this->assertSame(['title', 'url'], array_keys($result));
	}

	/**
	 * Test that anonymous function parameters are excluded
	 *
	 * @return void
	 */
	public function testExtractExcludesAnonymousFunctionParameters() {
		$content = <<<'PHP'
<?php
$yourMoodIds = array_map(function($m) { return $m->mood_id; }, $participantMoods ?? []);
$filtered = array_filter($items, function($item) {
	return $item->active;
});
usort($data, function($a, $b) {
	return $a->sort_order <=> $b->sort_order;
});
$doubled = array_map(fn($x) => $x * 2, $numbers);

// Test with 'use' clause
$multiplier = 5;
$result = array_map(function($n) use ($multiplier) {
	return $n * $multiplier;
}, $values);
PHP;

		$file = $this->getFile('', $content);

		$result = $this->variableExtractor->extract($file);

		// Check that regular variables are found with correct exclusion reason
		$regularVars = ['yourMoodIds', 'participantMoods', 'filtered', 'items', 'data', 'doubled', 'numbers', 'multiplier', 'result', 'values'];
		foreach ($regularVars as $var) {
			$this->assertArrayHasKey($var, $result, "Variable \$$var should be found");
			if (in_array($var, ['yourMoodIds', 'filtered', 'doubled', 'multiplier', 'result'])) {
				$this->assertEquals('Assignment', $result[$var]['excludeReason'], "Variable \$$var should be excluded as assignment");
			} else {
				$this->assertNull($result[$var]['excludeReason'], "Variable \$$var should not be excluded");
			}
		}

		// Check that anonymous function parameters are excluded
		$functionParams = ['m', 'item', 'a', 'b', 'x', 'n'];
		foreach ($functionParams as $param) {
			$this->assertArrayHasKey($param, $result, "Parameter \$$param should be found");
			$this->assertEquals('Anonymous function parameter', $result[$param]['excludeReason'], "Parameter \$$param should be excluded as anonymous function parameter");
		}
	}

}
