<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Task\EntityFieldTask;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class EntityFieldTaskTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Tools\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $io;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);
	}

	/**
	 * @return void
	 */
	public function testIsApplicable() {
		$task = $this->_getTask();

		$result = $task->isApplicable('src/Model/Entity/Wheel.php');
		$this->assertTrue($result);

		$result = $task->isApplicable('src/Model/Table/Wheels.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testIlluminate() {
		$task = $this->_getTask([
			'visibility' => false,
		]);

		$path = TEST_FILES . 'Model/Entity/Wheel.php';
		$result = $task->run(file_get_contents($path), $path);

		$this->assertTextContains('const FIELD_ID = \'id\';', $result);

		$expected = file_get_contents(TEST_FILES . 'Model/Entity/Constants/Wheel.php');
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
