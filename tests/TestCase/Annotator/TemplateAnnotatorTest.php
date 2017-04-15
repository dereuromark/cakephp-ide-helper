<?php

namespace IdeHelper\Test\TestCase\Annotator;

use App\Model\Table\FooTable;
use Cake\Console\ConsoleIo;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Console\Io;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

/**
 */
class TemplateAnnotatorTest extends TestCase {

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
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);

		$x = TableRegistry::get('IdeHelper.Foo', ['className' => FooTable::class]);
		$columns = [
			'id' => [
				'type' => 'integer',
				'length' => 11,
				'unsigned' => false,
				'null' => false,
				'default' => null,
				'comment' => '',
				'autoIncrement' => true,
				'baseType' => null,
				'precision' => null
			],
		];
		$schema = new TableSchema('Foo', $columns);
		$x->setSchema($schema);
		TableRegistry::set('Foo', $x);
	}

	/**
	 * Tests create() parsing part and creating a new PHP tag in first line.
	 *
	 * @return void
	 */
	public function testAnnotate() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Template/edit.ctp'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Template/Foos/edit.ctp';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 2 annotations added', $output);
	}

	/**
	 * Tests loop and entity->field, as well as writing into an existing PHP tag.
	 *
	 * @return void
	 */
	public function testAnnotateLoop() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Template/loop.ctp'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Template/Foos/loop.ctp';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 2 annotations added', $output);
	}

	/**
	 * Tests loop and entity->field, as well as writing into an existing PHP tag.
	 *
	 * @return void
	 */
	public function testAnnotatePhpLine() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Template/phpline.ctp'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Template/Foos/phpline.ctp';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 3 annotations added', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block.
	 *
	 * @return void
	 */
	public function testAnnotateExisting() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Template/existing.ctp'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Template/Foos/existing.ctp';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 2 annotations added', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block and replacing outdated annotations.
	 *
	 * @return void
	 */
	public function testAnnotateExistingOutdated() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Template/outdated.ctp'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Template/Foos/outdated.ctp';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 2 annotations added', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\TemplateAnnotator|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [AbstractAnnotator::CONFIG_DRY_RUN => true];
		return $this->getMockBuilder(TemplateAnnotator::class)->setMethods(['_storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
