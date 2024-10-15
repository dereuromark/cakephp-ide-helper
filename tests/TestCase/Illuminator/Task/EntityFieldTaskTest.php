<?php

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Task\EntityFieldTask;
use Shim\TestSuite\ConsoleOutput;

class EntityFieldTaskTest extends TestCase {

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected Io $io;

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
	public function testShouldRun() {
		$task = $this->_getTask();

		$result = $task->shouldRun('src/Model/Entity/Wheel.php');
		$this->assertTrue($result);

		$result = $task->shouldRun('src/Model/Table/Wheels.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testIlluminate() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = APP . 'Model/Entity/Complex/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$this->assertTextContains('const FIELD_ID = \'id\';', $result);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/Constants/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateComplex() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = APP . 'Model/Entity/Complex/Wheel.php';
		$this->assertFileExists($path);
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/Constants/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateComplex2() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = APP . 'Model/Entity/Complex2/Wheel.php';
		$this->assertFileExists($path);
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/Constants/WheelComplex.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateExisting() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = TEST_FILES . 'Model/Entity/Constants/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/Constants/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateExistingPartial() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = TEST_FILES . 'Model/Entity/ConstantsPartial/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/ConstantsPartialResult/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateVisibility() {
		$task = $this->_getTask([
			'visibility' => true,
		]);

		$path = TEST_FILES . 'Model/Entity/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$this->assertTextContains('public const FIELD_ID = \'id\';', $result);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Illuminator\Task\EntityFieldTask
	 */
	protected function _getTask(array $params = []) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new EntityFieldTask($params);
	}

}
