<?php

namespace IdeHelper\Test\TestCase\Annotator;

use App\Model\Table\FooTable;
use Cake\Console\ConsoleIo;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\EntityAnnotator;
use IdeHelper\Console\Io;
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
	 * @var \Cake\Console\ConsoleIo
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
	public function testAnnotate() {
		$schema = TableRegistry::get('Foo')->getSchema();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Foo.php'));
		$callback = function($value) use ($expectedContent) {
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

		$this->assertTextContains('   -> 3 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithExistingDocBlock() {
		$schema = TableRegistry::get('Foo')->getSchema();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Car.php'));
		$callback = function($value) use ($expectedContent) {
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

		$this->assertTextContains('   -> 4 annotations added', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\EntityAnnotator|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [AbstractAnnotator::CONFIG_DRY_RUN => true];
		return $this->getMockBuilder(EntityAnnotator::class)->setMethods(['_storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
