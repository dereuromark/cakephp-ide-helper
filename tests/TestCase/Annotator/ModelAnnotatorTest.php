<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use TestApp\Model\Table\FoosTable;

class ModelAnnotatorTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.IdeHelper.Foos',
		'plugin.IdeHelper.Wheels',
		'plugin.IdeHelper.BarBars',
	];

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

		Configure::write('IdeHelper.assocsAsGenerics', true);
		Configure::write('IdeHelper.tableBehaviors', true);

		$x = TableRegistry::getTableLocator()->get('IdeHelper.Foos', ['className' => FoosTable::class]);
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
				'precision' => null,
			],
			'name' => [
				'type' => 'string',
				'length' => 100,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
				'fixed' => null,
			],
			'content' => [
				'type' => 'string',
				'length' => 100,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
				'fixed' => null,
			],
			'created' => [
				'type' => 'datetime',
				'length' => null,
				'null' => true,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
			],
			'params' => [
				'type' => 'json',
				'length' => null,
				'null' => true,
				'default' => null,
				'comment' => '',
				'precision' => null,
			],
		];
		$schema = new TableSchema('Foos', $columns);
		$x->setSchema($schema);
		TableRegistry::getTableLocator()->set('Foos', $x);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		Configure::delete('IdeHelper.assocsAsGenerics');
		Configure::delete('IdeHelper.tableBehaviors');
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ModelAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return $this->getMockBuilder(ModelAnnotator::class)->onlyMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/BarBarsTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/BarBarsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 18 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateExistingMerge() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/WheelsTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/WheelsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 15 annotations added, 1 annotation updated', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateExistingReplace() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/WheelsExtraTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/WheelsExtraTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 1 annotation updated', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSkip() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/SkipSomeTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/SkipSomeTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSkipAll() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/SkipMeTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/SkipMeTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateCatchExceptions() {
		$annotator = $this->_getAnnotatorMock([]);

		$path = APP . 'Model/Table/ExceptionsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextNotContains('annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateProtectedParent() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/BarBarsAbstractTable.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->any())
			->method('storeFile')
			->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/BarBarsAbstractTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextContains('  -> 18 annotations added', $output);
	}

}
