<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\EntityAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\View\Helper\DocBlockHelper;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestTrait;
use TestApp\Model\Table\FooTable;

class EntityAnnotatorTest extends TestCase {

	use DiffHelperTrait;
	use TestTrait;

	/**
	 * @var array
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Foo',
	];

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
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
			],
			'modified' => [
				'type' => 'datetime',
				'length' => null,
				'null' => true,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
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
		Configure::write('IdeHelper.nullableMap', [
			'custom' => false,
		]);

		$propertySchema = [
			'invalid' => [
				'kind' => 'column',
				'type' => 'invalid',
			],
			'custom' => [
				'kind' => 'column',
				'type' => 'custom',
				'null' => false,
			],
			'json' => [
				'kind' => 'column',
				'type' => 'json',
				'null' => true,
			],
			'resetted' => [
				'kind' => 'column',
				'type' => 'longtext',
			],
		];
		$helper = new DocBlockHelper(new View());

		/** @uses \IdeHelper\Annotator\EntityAnnotator::buildExtendedEntityPropertyHintTypeMap() */
		$result = $this->invokeMethod($annotator, 'buildExtendedEntityPropertyHintTypeMap', [$propertySchema, $helper]);
		$expected = [
			'custom' => 'array',
			'json' => 'array|null',
		];
		$this->assertSame($result, $expected);
	}

	/**
	 * @return void
	 */
	public function testAnnotate() {
		/** @var \TestApp\Model\Table\FooTable $Table */
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
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Foo.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 3 annotations added, 1 annotation updated.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithExistingDocBlock() {
		/** @var \TestApp\Model\Table\FooTable $Table */
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
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Car.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 6 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualProperties() {
		/** @var \TestApp\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');
		$Table->hasMany('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Wheel.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Wheel.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 7 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualPropertiesReadOnly() {
		/** @var \TestApp\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');
		$Table->hasMany('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Virtual.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Virtual.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 8 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualPropertiesAndReturnTypes() {
		/** @var \TestApp\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');
		$Table->hasMany('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/PHP7/Virtual.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/PHP7/Virtual.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 8 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateHasOne() {
		/** @var \Relations\Model\Table\UsersTable $Table */
		$Table = TableRegistry::get('Relations.Users');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Relations/User.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = PLUGINS . 'Relations/src/Model/Entity/User.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 4 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateBelongsToRequired() {
		/** @var \Relations\Model\Table\UsersTable $Table */
		$Table = TableRegistry::get('Relations.Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Relations/Foo.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = PLUGINS . 'Relations/src/Model/Entity/Foo.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 4 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateBelongsToNullable() {
		/** @var \Relations\Model\Table\BarsTable $Table */
		$Table = TableRegistry::get('Relations.Bars');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Relations/Bar.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = PLUGINS . 'Relations/src/Model/Entity/Bar.php';
		$annotator->annotate($path);

		$output = (string)$this->out->output();

		$this->assertTextContains('   -> 4 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithGenericUsage() {
		/** @var \TestApp\Model\Table\FooTable $Table */
		$Table = TableRegistry::get('Foo');
		$Table->hasMany('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/PHP/Generics.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/PHP/Generics.php';

		Configure::write('IdeHelper.arrayAsGenerics', true);

		$annotator->annotate($path);

		Configure::delete('IdeHelper.arrayAsGenerics');

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation updated', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\EntityAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(EntityAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
