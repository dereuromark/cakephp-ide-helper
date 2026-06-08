<?php

namespace IdeHelper\Test\TestCase\Annotator\ClassAnnotatorTask;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ClassAnnotatorTask\PropertyTypeAnnotatorTask;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class PropertyTypeAnnotatorTaskTest extends TestCase {

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected Io $io;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Configure::write('IdeHelper.propertyTypeMap', [
			'actsAs' => 'array<string, mixed>',
			'helpers' => 'array<int|string, string|array<string, mixed>>',
			'components' => 'array<int|string, string|array<string, mixed>>',
			'paginate' => 'array<string, mixed>',
		]);

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		Configure::delete('IdeHelper.propertyTypeMap');

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testShouldRun(): void {
		$task = $this->getTask('');

		$result = $task->shouldRun('/src/Foo.php', '');
		$this->assertFalse($result);

		$result = $task->shouldRun('/tests/Foo.php', 'class Foo { public array $actsAs = []; }');
		$this->assertFalse($result);

		$result = $task->shouldRun('/src/Foo.php', 'class Foo { public array $actsAs = []; }');
		$this->assertTrue($result);
	}

	/**
	 * @return void
	 */
	public function testAnnotate(): void {
		$content = <<<'PHP'
<?php
namespace TestApp\Model\Table;

class FooTable {
	public array $actsAs = [];

	protected array $helpers = [];

	public array $components = [];

	protected array $paginate = [];
}
PHP;

		$task = $this->getTask($content);

		$result = $task->annotate('/src/Model/Table/FooTable.php');
		$this->assertTrue($result);

		$content = $task->getContent();
		$this->assertMatchesRegularExpression('#/\*\* @var array<string, mixed> \$actsAs \*/\R\s+public array \$actsAs = \[\];#', $content);
		$this->assertMatchesRegularExpression('#/\*\* @var array<int\|string, string\|array<string, mixed>> \$helpers \*/\R\s+protected array \$helpers = \[\];#', $content);
		$this->assertMatchesRegularExpression('#/\*\* @var array<int\|string, string\|array<string, mixed>> \$components \*/\R\s+public array \$components = \[\];#', $content);
		$this->assertMatchesRegularExpression('#/\*\* @var array<string, mixed> \$paginate \*/\R\s+protected array \$paginate = \[\];#', $content);
	}

	/**
	 * @return void
	 */
	public function testAnnotateAlreadyAnnotated(): void {
		$content = <<<'PHP'
<?php
namespace TestApp\Model\Table;

class FooTable {
	/** @var array<string, mixed> */
	public array $actsAs = [];
}
PHP;

		$task = $this->getTask($content);

		$result = $task->annotate('/src/Model/Table/FooTable.php');
		$this->assertFalse($result);
	}

	/**
	 * @param string $content
	 * @param array<string, mixed> $params
	 * @return \IdeHelper\Annotator\ClassAnnotatorTask\PropertyTypeAnnotatorTask
	 */
	protected function getTask(string $content, array $params = []): PropertyTypeAnnotatorTask {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new PropertyTypeAnnotatorTask($this->io, $params, $content);
	}

}
