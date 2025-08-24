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

/**
 * Test with concreteEntitiesInParam config on.
 */
class ModelAnnotatorSpecificTest extends TestCase {

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
		Configure::write('IdeHelper.concreteEntitiesInParam', true);
		Configure::write('IdeHelper.genericsInParam', true);
		Configure::write('IdeHelper.tableBehaviors', 'mixin');

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
		Configure::delete('IdeHelper.concreteEntitiesInParam');
		Configure::delete('IdeHelper.genericsInParam');
		Configure::delete('IdeHelper.tableBehaviors');
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ModelAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params): ModelAnnotator {
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
	public function testAnnotateSpecific() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/BarBarsTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/Specific/BarBarsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 7 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificExistingMerge() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/WheelsTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/Specific/WheelsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 14 annotations added, 1 annotation updated', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificExistingReplace() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/WheelsExtraTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/Specific/WheelsExtraTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('  -> 1 annotation updated', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificSkip() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/SkipSomeTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/Specific/SkipSomeTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificSkipAll() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/SkipMeTable.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/Specific/SkipMeTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertSame('', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificCatchExceptions() {
		$annotator = $this->_getAnnotatorMock([]);

		$path = APP . 'Model/Table/Specific/ExceptionsTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextNotContains('annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateSpecificProtectedParent() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/Specific/BarBarsAbstractTable.php'));
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

		$path = APP . 'Model/Table/Specific/BarBarsAbstractTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextContains('  -> 17 annotations added', $output);
	}

}
