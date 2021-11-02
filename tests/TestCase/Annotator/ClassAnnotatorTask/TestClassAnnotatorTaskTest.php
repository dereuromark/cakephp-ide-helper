<?php

namespace IdeHelper\Test\TestCase\Annotator\ClassAnnotatorTask;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class TestClassAnnotatorTaskTest extends TestCase {

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $io;

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
		$task = $this->getTask('');

		$content = 'namespace TestApp\Test\TestCase\Controller\FooControllerTest.php' . PHP_EOL . 'class FooControllerTest extends ControllerIntegrationTestCase';
		$result = $task->shouldRun('/tests/TestCase/Foo.php', $content);
		$this->assertTrue($result);

		$content = 'namespace TestApp\Test\TestCase\Command\FooCommandTest.php' . PHP_EOL . 'class FooCommandTest extends ConsoleIntegrationTestCase';
		$result = $task->shouldRun('/tests/TestCase/Foo.php', $content);
		$this->assertTrue($result);

		$result = $task->shouldRun('/tests/TestCase/Foo.php', 'namespace TestApp\Foo\Foo.php');
		$this->assertFalse($result);

		$result = $task->shouldRun('/tests/Foo.php', $content);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$content = file_get_contents(TEST_FILES . 'tests' . DS . 'BarControllerTest.new.php');
		$task = $this->getTask($content);
		$path = '/tests/TestCase/Controller/BarControllerTest.php';

		$result = $task->annotate($path);
		$this->assertTrue($result);

		$content = $task->getContent();
		$this->assertTextContains('* @uses \TestApp\Controller\BarController', $content);

		$output = $this->out->output();
		$this->assertTextContains('  -> 1 annotation added.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateExisting() {
		$content = file_get_contents(TEST_FILES . 'tests' . DS . 'BarControllerTest.existing.php');
		$task = $this->getTask($content);
		$path = '/tests/TestCase/Controller/BarControllerTest.php';

		$result = $task->annotate($path);
		$this->assertFalse($result);

		$content = $task->getContent();
		$count = substr_count($content, '@uses');
		$this->assertSame(1, $count);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @param string $content
	 * @param array $params
	 *
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\TestClassAnnotatorTask
	 */
	protected function getTask($content, array $params = []) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new TestClassAnnotatorTask($this->io, $params, $content);
	}

}
