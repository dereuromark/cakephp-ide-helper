<?php

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
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
	 * Typed class constants (PHP 8.3+) must be detected as existing.
	 *
	 * @return void
	 */
	public function testIlluminateExistingPartialTyped() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = TEST_FILES . 'Model/Entity/ConstantsTypedPartial/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/ConstantsTypedPartialResult/Wheel.php');
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
	 * @return void
	 */
	public function testIlluminateTyped() {
		$task = $this->_getTask([
			'visibility' => true,
			'typed' => true,
		]);

		$path = TEST_FILES . 'Model/Entity/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$this->assertTextContains('public const string FIELD_ID = \'id\';', $result);
	}

	/**
	 * Existing untyped constants get the type added, new ones are added typed.
	 *
	 * @return void
	 */
	public function testIlluminateTypedExistingPartial() {
		$task = $this->_getTask([
			'visibility' => false,
			'typed' => true,
		]);

		$path = TEST_FILES . 'Model/Entity/ConstantsPartial/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/ConstantsTypedResult/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * Already typed constants stay untouched, also when no new fields need to be added.
	 *
	 * @return void
	 */
	public function testIlluminateTypedExisting() {
		$task = $this->_getTask([
			'visibility' => false,
			'typed' => true,
		]);

		$path = TEST_FILES . 'Model/Entity/ConstantsTypedResult/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$result = str_replace('    ', "\t", $result);
		$expected = file_get_contents(TEST_FILES . 'Model/Entity/ConstantsTypedResult/Wheel.php');
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * Only string literal values get the type added, others would be invalid.
	 *
	 * @return void
	 */
	public function testIlluminateTypedSkipsNonStringValues() {
		$task = $this->_getTask([
			'visibility' => false,
			'typed' => true,
		]);

		$path = APP . 'Model/Entity/Complex/Wheel.php';
		$content = str_replace(
			'class Wheel extends Entity {',
			"class Wheel extends Entity {\n\n\tconst FIELD_ID = 1;\n\tconst FIELD_NAME = self::OTHER;\n\tpublic const FIELD_CONTENT = 'content';",
			(string)file_get_contents($path),
		);
		$result = $task->run($content, $path);

		$this->assertTextContains('const FIELD_ID = 1;', $result);
		$this->assertTextContains('const FIELD_NAME = self::OTHER;', $result);
		$this->assertTextContains('public const string FIELD_CONTENT = \'content\';', $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateTypedViaConfigure() {
		Configure::write('IdeHelper.illuminatorTypedConstants', true);

		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = TEST_FILES . 'Model/Entity/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		Configure::delete('IdeHelper.illuminatorTypedConstants');

		$this->assertTextContains('const string FIELD_ID = \'id\';', $result);
	}

	/**
	 * @return void
	 */
	public function testIlluminateNotTypedByDefault() {
		$task = $this->_getTask();

		$path = TEST_FILES . 'Model/Entity/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$this->assertTextContains('public const FIELD_ID = \'id\';', $result);
		$this->assertTextNotContains('const string', $result);
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
