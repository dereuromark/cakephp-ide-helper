<?php

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Illuminator\Task\FieldConstantUsageTask;

class FieldConstantUsageTaskTest extends TestCase {

	/**
	 * @return void
	 */
	public function testShouldRun(): void {
		$task = $this->getTask();

		$result = $task->shouldRun('src/Model/Table/VehiclesTable.php');
		$this->assertTrue($result);

		$result = $task->shouldRun('src/Model/Table/Table.php');
		$this->assertFalse($result);

		$result = $task->shouldRun('src/Model/Entity/Vehicle.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testRun(): void {
		$task = $this->getTask();

		$path = TEST_FILES . 'Model/Table/FieldConstantUsage/VehiclesTable.php';
		$content = file_get_contents($path);
		$this->assertIsString($content);

		$result = $task->run($content, $path);

		$this->assertStringContainsString('Vehicle::FIELD_NAME', $result);
		$this->assertStringContainsString('Vehicle::FIELD_ID', $result);
		$this->assertStringContainsString('Vehicle::FIELD_CONTENT', $result);
		$this->assertStringContainsString('Vehicle::FIELD_CREATED', $result);
		$this->assertStringContainsString('Vehicle::FIELD_MODIFIED', $result);
		$this->assertStringContainsString('use TestApp\Model\Entity\Vehicle;', $result);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Table/FieldConstantUsageResult/VehiclesTable.php');
		$this->assertIsString($expected);
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testRunNoChanges(): void {
		$task = $this->getTask();

		// Test with already converted file
		$path = TEST_FILES . 'Model/Table/FieldConstantUsageResult/VehiclesTable.php';
		$content = file_get_contents($path);
		$this->assertIsString($content);

		$result = $task->run($content, $path);

		// Should not modify already converted code
		$this->assertEquals($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunNoEntityConstants(): void {
		$task = $this->getTask();

		$content = <<<'PHP'
<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

class UnknownTable extends Table {
	public function findSomething($query) {
		return $query->where(['unknown_field' => 'value']);
	}
}
PHP;

		$result = $task->run($content, 'src/Model/Table/UnknownTable.php');

		// No changes since entity has no constants
		$this->assertEquals($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunSkipsDottedFields(): void {
		$task = $this->getTask();

		$content = <<<'PHP'
<?php
namespace TestApp\Model\Table;

use Cake\ORM\Table;

class VehiclesTable extends Table {
	public function findWithJoin($query) {
		return $query->where(['Vehicles.name' => 'test', 'Cars.name' => 'test']);
	}
}
PHP;

		$result = $task->run($content, 'src/Model/Table/VehiclesTable.php');

		// Should not replace dotted field names (table.field notation)
		$this->assertStringContainsString("'Vehicles.name'", $result);
		$this->assertStringContainsString("'Cars.name'", $result);
	}

	/**
	 * @param array<string, mixed> $params
	 * @return \IdeHelper\Illuminator\Task\FieldConstantUsageTask
	 */
	protected function getTask(array $params = []): FieldConstantUsageTask {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new FieldConstantUsageTask($params);
	}

}
