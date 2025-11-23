<?php

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Illuminator\Task\TableValidationLinkTask;

class TableValidationLinkTaskTest extends TestCase {

	/**
	 * @return void
	 */
	public function testShouldRun(): void {
		$task = $this->getTask();

		$result = $task->shouldRun('src/Model/Table/WheelsTable.php');
		$this->assertTrue($result);

		$result = $task->shouldRun('src/Model/Table/Table.php');
		$this->assertFalse($result);

		$result = $task->shouldRun('src/Model/Entity/Wheel.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testRun(): void {
		$task = $this->getTask();

		$path = TEST_FILES . 'Model/Table/Validation/IpRulesTable.php';
		$content = file_get_contents($path);
		$this->assertIsString($content);

		$result = $task->run($content, $path);

		$this->assertStringContainsString('/** @link verifyIpRanges() */', $result);
		$this->assertStringContainsString('/** @link verifyDenyRanges() */', $result);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Table/ValidationResult/IpRulesTable.php');
		$this->assertIsString($expected);
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testRunNoChanges(): void {
		$task = $this->getTask();

		// Test with already annotated file
		$path = TEST_FILES . 'Model/Table/ValidationResult/IpRulesTable.php';
		$content = file_get_contents($path);
		$this->assertIsString($content);

		$result = $task->run($content, $path);

		// Should not add duplicate annotations
		$this->assertEquals($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunNoTableProvider(): void {
		$task = $this->getTask();

		$content = <<<'PHP'
<?php
class FooTable {
	public function validationDefault($validator) {
		$validator->add('email', 'valid', [
			'rule' => 'email',
		]);
		return $validator;
	}
}
PHP;

		$result = $task->run($content, 'src/Model/Table/FooTable.php');

		// No @link should be added (no table provider)
		$this->assertStringNotContainsString('@link', $result);
		$this->assertEquals($content, $result);
	}

	/**
	 * @param array<string, mixed> $params
	 * @return \IdeHelper\Illuminator\Task\TableValidationLinkTask
	 */
	protected function getTask(array $params = []): TableValidationLinkTask {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new TableValidationLinkTask($params);
	}

}
