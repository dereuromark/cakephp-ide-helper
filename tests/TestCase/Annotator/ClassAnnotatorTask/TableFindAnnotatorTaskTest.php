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
	public function testShouldRun(): void {
		$task = $this->getTask('');

		$result = $task->shouldRun('/src/Foo.php', '');
		$this->assertFalse($result);

		$result = $task->shouldRun('/src/Foo.php', '$x = $this->Table->find()->first();');
		$this->assertTrue($result);

		$result = $task->shouldRun('/src/Foo.php', '$x = $this->Table->find()->firstOrFail();');
		$this->assertTrue($result);

		$result = $task->shouldRun('/tests/Foo.php', '$x = $this->Table->find()->first();');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotate(): void {
		$content = file_get_contents(TEST_FILES . 'ClassAnnotation/TableFind/before.php');
		$this->assertIsString($content);

		$task = $this->getTask($content);
		$path = '/src/Controller/TestMeController.php';

		$result = $task->annotate($path);
		$this->assertTrue($result);

		$content = $task->getContent();
		// Only $this->Residents patterns should be detected
		$this->assertStringContainsString('/** @var \TestApp\Model\Entity\Resident|null $residentX */', $content);
		$this->assertStringContainsString('/** @var \TestApp\Model\Entity\Resident $residentY */', $content);

		// The $residentsTable patterns should NOT have annotations (not $this->TableName)
		$this->assertStringNotContainsString('@var \TestApp\Model\Entity\Resident|null $resident */', $content);
		$this->assertStringNotContainsString('@var \TestApp\Model\Entity\Resident $residentOther */', $content);
	}

	/**
	 * @return void
	 */
	public function testAnnotateNoMatches(): void {
		$content = <<<'PHP'
<?php
class FooController {
	public function test(): void {
		$table = $this->fetchTable('Users');
		$user = $table->find()->first();
	}
}
PHP;

		$task = $this->getTask($content);
		$path = '/src/Controller/FooController.php';

		$result = $task->annotate($path);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotateAlreadyAnnotated(): void {
		$content = <<<'PHP'
<?php
class FooController {
	public function test(): void {
		/** @var \App\Model\Entity\User|null $user */
		$user = $this->Users->find()->first();
	}
}
PHP;

		$task = $this->getTask($content);
		$path = '/src/Controller/FooController.php';

		$result = $task->annotate($path);
		$this->assertFalse($result);
	}

	/**
	 * @param string $content
	 * @param array<string, mixed> $params
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\TableFindAnnotatorTask
	 */
	protected function getTask(string $content, array $params = []): TableFindAnnotatorTask {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new TableFindAnnotatorTask($this->io, $params, $content);
	}

}
