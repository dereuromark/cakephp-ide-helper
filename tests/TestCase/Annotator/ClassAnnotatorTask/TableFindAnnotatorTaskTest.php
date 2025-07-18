<?php

namespace IdeHelper\Test\TestCase\Annotator\ClassAnnotatorTask;

use Cake\Console\ConsoleIo;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ClassAnnotatorTask\TableFindAnnotatorTask;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class TableFindAnnotatorTaskTest extends TestCase {

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected ?Io $io = null;

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

		$result = $task->shouldRun('/src/Foo.php', '');
		$this->assertTrue($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$content = file_get_contents(TEST_FILES . 'ClassAnnotation/TableFind/before.php');
		$task = $this->getTask($content);
		$path = '/src/Controller/TestMeController.php';

		$result = $task->annotate($path);
		$this->assertTrue($result);

		$content = $task->getContent();
		dd($content);
		$this->assertTextContains('/** @var \App\Model\Entity\Resident $resident */', $content);

		$output = $this->out->output();
		$this->assertTextContains('  -> 1 annotation added.', $output);
	}

	/**
	 * @param string $content
	 * @param array $params
	 *
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\TableFindAnnotatorTask
	 */
	protected function getTask(string $content, array $params = []) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new TableFindAnnotatorTask($this->io, $params, $content);
	}

}
