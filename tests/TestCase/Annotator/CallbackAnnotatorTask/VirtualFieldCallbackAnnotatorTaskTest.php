<?php

namespace IdeHelper\Test\TestCase\Annotator\CallbackAnnotatorTask;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\CallbackAnnotatorTask\VirtualFieldCallbackAnnotatorTask;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class VirtualFieldCallbackAnnotatorTaskTest extends TestCase {

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
		$task = $this->getTask('', '');

		$result = $task->shouldRun('/src/Model/Entity/Foo1.php');
		$this->assertTrue($result);

		$result = $task->shouldRun('/src/Model/Table/Foo2.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$content = file_get_contents(TEST_FILES . 'VirtualFieldAnnotation' . DS . 'VirtualFieldAnnotation.missing.php');
		$path = '/src/Model/Entity/Foo.php';
		$task = $this->getTask($path, $content);

		$result = $task->annotate($path);
		$this->assertTrue($result);

		$content = $task->getContent();
		$this->assertTextContains('* @see \TestApp\Model\Entity\Foo::$expected_release_type', $content);
	}

	/**
	 * @return void
	 */
	public function testAnnotateExisting() {
		$content = file_get_contents(TEST_FILES . 'VirtualFieldAnnotation' . DS . 'VirtualFieldAnnotation.existing.php');
		$path = '/src/Model/Entity/Foo.php';
		$task = $this->getTask($path, $content);

		$result = $task->annotate($path);
		$this->assertTrue($result);

		$content = $task->getContent();
		$count = substr_count($content, '@see');
		// We cannot avoid the duplication for incomplete tags for now
		$this->assertSame(3, $count, 'Count is ' . $count);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param array $params
	 *
	 * @return \IdeHelper\Annotator\CallbackAnnotatorTask\VirtualFieldCallbackAnnotatorTask
	 */
	protected function getTask(string $path, string $content, array $params = []) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new VirtualFieldCallbackAnnotatorTask($this->io, $params, $path, $content);
	}

}
