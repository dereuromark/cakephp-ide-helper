<?php

namespace IdeHelper\Test\TestCase\Annotator;

use App\Model\Table\FooTable;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\EntityAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\View\Helper\DocBlockHelper;
use Tools\TestSuite\ConsoleOutput;
use Tools\TestSuite\TestCase;

class EntityAnnotatorTest extends TestCase {

	use DiffHelperTrait;

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.ide_helper.foo'
	];

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

		Configure::delete('IdeHelper');

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
			'name' => [
				'type' => 'string',
				'length' => 100,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
				'fixed' => null
			],
			'content' => [
				'type' => 'string',
				'length' => 100,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
				'fixed' => null
			],
			'created' => [
				'type' => 'datetime',
				'length' => null,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null
			],
			'modified' => [
				'type' => 'datetime',
				'length' => null,
				'null' => true,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null
			],
		];
		$schema = new TableSchema('Foo', $columns);
		$x->setSchema($schema);
		TableRegistry::set('Foo', $x);
	}

	/**
	 * @return void
	 */
	public function testBuildExtendedEntityPropertyHintTypeMap() {
		$config = [];
		$annotator = new EntityAnnotator(new Io(new ConsoleIo()), $config);

		Configure::write('IdeHelper.typeMap', [
			'custom' => 'array',
			'longtext' => null,
		]);

		$propertySchema = [
			'invalid' => [
				'kind' => 'column',
				'type' => 'invalid',
			],
			'custom' => [
				'kind' => 'column',
				'type' => 'custom',
			],
			'json' => [
				'kind' => 'column',
				'type' => 'json',
			],
			'resetted' => [
				'kind' => 'column',
				'type' => 'longtext',
			],
		];
		$helper = new DocBlockHelper(new View());

		$result = $this->invokeMethod($annotator, 'buildExtendedEntityPropertyHintTypeMap', [$propertySchema, $helper]);
		$expected = [
			'custom' => 'array',
			'json' => 'array',
		];
		$this->assertSame($result, $expected);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		/** @var \App\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Foo.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Foo.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 3 annotations added, 1 annotation updated.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithExistingDocBlock() {
		/** @var \App\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');
		$Table->hasMany('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Car.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}
			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('_storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Car.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 6 annotations added', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\EntityAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true
		];
		return $this->getMockBuilder(EntityAnnotator::class)->setMethods(['_storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
