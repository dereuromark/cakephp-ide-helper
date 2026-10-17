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
use IdeHelper\Test\Generated\IdeHelperNativeFieldsTestEntity;
use IdeHelper\View\Helper\DocBlockHelper;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestTrait;
use TestApp\Model\Table\FoosTable;

class EntityAnnotatorTest extends TestCase {

	use DiffHelperTrait;
	use TestTrait;

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected Io $io;

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
			'offer_date' => [
				'type' => 'date',
				'length' => null,
				'null' => false,
				'default' => null,
				'comment' => '',
				'baseType' => null,
				'precision' => null,
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
	protected function tearDown(): void {
		TableRegistry::getTableLocator()->allowFallbackClass(true);

		parent::tearDown();
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
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

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

		$this->assertTextContains('   -> 4 annotations added, 1 annotation updated.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithExistingDocBlock() {
		/** @var \TestApp\Model\Table\CarsTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Cars');

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

		$this->assertTextContains('   -> 7 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithExistingDocBlockComplex() {
		/** @var \TestApp\Model\Table\WheelsTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Wheels');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/Complex/Wheel.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/Complex/Wheel.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added, 3 annotations removed, 1 annotation skipped.', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualProperties() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');
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

		$this->assertTextContains('   -> 9 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualPropertiesReadOnly() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');
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

		$this->assertTextContains('   -> 10 annotations added', $output);
	}

	/**
	 * A computed `_get*` field with no DB column gets `@property-read` only
	 * when it has no matching `_set*` mutator. A `_get*`/`_set*` pair is
	 * writable and must keep the plain `@property` tag.
	 *
	 * @return void
	 */
	public function testAnnotateWithVirtualMutator() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/VirtualWithMutator.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/VirtualWithMutator.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('annotations added', $output);
	}

	/**
	 * Migration: a stale tag from an older run is flipped in place. A
	 * `@property` on a read-only virtual field becomes `@property-read`, and a
	 * `@property-read` on a field that has gained a `_set*` mutator becomes
	 * `@property` again, even when the type is unchanged.
	 *
	 * @return void
	 */
	public function testAnnotateVirtualTagMigration() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/VirtualMigration.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/VirtualMigration.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('annotations', $output);
	}

	/**
	 * Regression test for trait-backed virtual properties: the
	 * tokenizer-based scan only sees `_get*` methods declared in
	 * the entity's own file body, so trait-imported `_get*` methods
	 * used to be invisible — and `--remove` would strip the manual
	 * `@property-read` lines for them.
	 *
	 * The reflection fallback in buildInheritedVirtualPropertyHintTypeMap()
	 * picks them up. Verifies trait-virtuals AND in-class virtuals
	 * both end up in the generated docblock.
	 *
	 * @return void
	 */
	public function testAnnotateWithTraitBackedVirtualProperties() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/VirtualWithTrait.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/VirtualWithTrait.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithVirtualPropertiesAndReturnTypes() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');
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

		$this->assertTextContains('   -> 11 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateHasOne() {
		/** @var \Relations\Model\Table\UsersTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Relations.Users');

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
		$Table = TableRegistry::getTableLocator()->get('Relations.Foos');

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

		$this->assertTextContains('   -> 5 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateBelongsToNullable() {
		/** @var \Relations\Model\Table\BarsTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Relations.Bars');

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
	 * The `_joinData` association is synthetic, its `{Alias}Join` table class does not exist.
	 * Apps that ran `allowFallbackClass(false)` (the CakePHP app skeleton default) must not
	 * run into a MissingTableClassException for that made-up alias.
	 *
	 * @return void
	 */
	public function testAnnotateBelongsToManyWithoutFallbackClass() {
		TableRegistry::getTableLocator()->allowFallbackClass(false);

		/** @var \Relations\Model\Table\UsersTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Relations.Users');
		$Table->belongsToMany('Tags', ['className' => 'Relations.Bars']);

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$content = null;
		$callback = function ($value) use (&$content) {
			$content = $value;

			return true;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = PLUGINS . 'Relations/src/Model/Entity/User.php';
		$annotator->annotate($path);

		$this->assertTextContains('@property \Cake\ORM\Entity $_joinData', (string)$content);
	}

	/**
	 * The junction table comes from the locator and is shared. Registering the synthetic
	 * `{Alias}Join` association on it would survive this run, and the ModelAnnotator would
	 * later emit it as a `@property` on a table that never declared it.
	 *
	 * @return void
	 */
	public function testAnnotateBelongsToManyLeavesThroughTableUntouched() {
		/** @var \Relations\Model\Table\BarsTable $through */
		$through = TableRegistry::getTableLocator()->get('Relations.Bars');
		$associationsBefore = $through->associations()->keys();

		/** @var \Relations\Model\Table\UsersTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Relations.Users');
		$Table->belongsToMany('Tags', ['className' => 'Relations.Foos', 'through' => 'Relations.Bars']);

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$content = null;
		$callback = function ($value) use (&$content) {
			$content = $value;

			return true;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = PLUGINS . 'Relations/src/Model/Entity/User.php';
		$annotator->annotate($path);

		$this->assertTextContains('$_joinData', (string)$content);
		$this->assertSame($associationsBefore, $through->associations()->keys());
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithGenericUsage() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');
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

		$this->assertTextContains('   -> 1 annotation added, 1 annotation updated', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithDuplicates() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/PHP/Duplicates.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/PHP/Duplicates.php';

		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation removed', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithComplexType() {
		/** @var \TestApp\Model\Table\FoosTable $Table */
		$Table = TableRegistry::getTableLocator()->get('Foos');

		$schema = $Table->getSchema();
		$associations = $Table->associations();
		$annotator = $this->_getAnnotatorMock(['schema' => $schema, 'associations' => $associations]);

		$expectedContent = str_replace(["\r\n", "\r"], "\n", file_get_contents(TEST_FILES . 'Model/Entity/PHP/ComplexType.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Entity/PHP/ComplexType.php';

		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation skipped', $output);
	}

	/**
	 * @return void
	 */
	public function testNativeProperties(): void {
		Configure::write('IdeHelper.entityNativeProperties', true);
		Configure::write('IdeHelper.arrayAsGenerics', true);
		$table = TableRegistry::getTableLocator()->get('Foos');
		$table->belongsTo('ParentFoos', ['targetTable' => $table, 'foreignKey' => 'parent_id', 'propertyName' => 'parent_foo']);
		$table->hasMany('ChildFoos', ['targetTable' => $table, 'foreignKey' => 'parent_id', 'propertyName' => 'children']);
		$schema = $table->getSchema();
		$schema->addColumn('parent_id', ['type' => 'integer', 'null' => true]);
		$schema->addColumn('dirty', ['type' => 'string', 'null' => false]);
		$schema->addColumn('binary_data', ['type' => 'binary', 'null' => true]);
		$schema->addColumn('varbinary_data', ['type' => 'varbinary', 'null' => false]);
		$annotator = new EntityAnnotator($this->io, ['schema' => $schema, 'associations' => $table->associations()]);
		$path = TMP . 'IdeHelperNativeFieldsTestEntity.php';
		$content = <<<'PHP'
<?php
namespace IdeHelper\Test\Generated;

class IdeHelperNativeFieldsTestEntity extends \Cake\ORM\Entity {
	protected string $name;

	protected function _getContent(): string {
		return 'computed';
	}

	protected function _getLabel(): string {
		return 'label';
	}
}
PHP;
		file_put_contents($path, $content);
		try {
			$this->assertTrue($annotator->annotate($path));
			$result = file_get_contents($path);
			$this->assertStringContainsString('protected int $id;', $result);
			$this->assertStringContainsString('protected int|null $parent_id = null;', $result);
			$this->assertStringContainsString('protected \Cake\I18n\DateTime|null $modified = null;', $result);
			$this->assertStringContainsString('protected \TestApp\Model\Entity\Foo|null $parent_foo = null;', $result);
			$this->assertStringContainsString('protected array $children = [];', $result);
			$this->assertStringContainsString('@property string|resource $varbinary_data', $result);
			$this->assertStringNotContainsString('$varbinary_data;', $result);
			$this->assertStringContainsString('@var array<\TestApp\Model\Entity\Foo>', $result);
			$this->assertSame(1, substr_count($result, 'string $name;'));
			$this->assertStringNotContainsString('protected string $content;', $result);
			$this->assertStringNotContainsString('protected string $label;', $result);
			$this->assertStringNotContainsString('protected string $dirty;', $result);
			$this->assertStringNotContainsString('protected resource', $result);
			$this->assertFalse($annotator->annotate($path), $this->out->output());
			$this->assertSame($result, file_get_contents($path));

			// Exercise the generated code against CakePHP's property storage and magic writes.
			require $path;
			$entity = new IdeHelperNativeFieldsTestEntity(['id' => 1, 'modified' => null]);
			$this->assertSame(1, $entity->id);
			$this->assertNull($entity->modified);
			$this->assertFalse($entity->hasValue('name'));
			// Unset fields read as their defaults inside the entity, but are not reported as present.
			$this->assertNull((fn () => $this->parent_id)->call($entity));
			$this->assertSame([], (fn () => $this->children)->call($entity));
			$this->assertFalse($entity->has('parent_id'));
			$this->assertArrayNotHasKey('children', $entity->toArray());
			$entity->clean();
			$entity->id = 2;
			$this->assertTrue($entity->isDirty('id'));
			$this->assertSame(1, $entity->getOriginal('id'));
		} finally {
			unlink($path);
		}
	}

	/**
	 * @return void
	 */
	public function testNativePropertiesDryRunAndSkip(): void {
		Configure::write('IdeHelper.entityNativeProperties', true);
		$table = TableRegistry::getTableLocator()->get('Foos');
		$annotator = new EntityAnnotator($this->io, [
			'schema' => $table->getSchema(),
			'associations' => $table->associations(),
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		]);
		$path = TMP . 'NativeDryRun.php';
		$content = "<?php\nnamespace IdeHelper\\Test\\Generated;\n\nclass NativeDryRun extends \\Cake\\ORM\\Entity {\n}\n";
		file_put_contents($path, $content);
		try {
			$this->assertTrue($annotator->annotate($path));
			$this->assertSame($content, file_get_contents($path));
			$this->assertStringContainsString('protected int $id;', $this->out->output());

			$content = str_replace('class NativeDryRun', "/**\n * @inheritDoc\n */\nclass NativeDryRun", $content);
			file_put_contents($path, $content);
			$annotator->setConfig(AbstractAnnotator::CONFIG_DRY_RUN, false);
			$this->assertFalse($annotator->annotate($path), $this->out->output());
			$this->assertSame($content, file_get_contents($path));
		} finally {
			unlink($path);
		}
	}

	/**
	 * @return void
	 */
	public function testNativePropertiesRespectInheritedDeclarations(): void {
		Configure::write('IdeHelper.entityNativeProperties', true);
		$parent = new class extends \Cake\ORM\Entity {
			protected string $name;
		};
		$table = TableRegistry::getTableLocator()->get('Foos');
		$annotator = new EntityAnnotator($this->io, [
			'class' => get_class($parent),
			'schema' => $table->getSchema(),
			'associations' => $table->associations(),
		]);
		$path = TMP . 'NativeInherited.php';
		file_put_contents($path, "<?php\nnamespace IdeHelper\\Test\\Generated;\n\nclass NativeInherited extends \\Cake\\ORM\\Entity {\n    protected int \$custom;\n}\n");
		try {
			$this->assertTrue($annotator->annotate($path));
			$result = file_get_contents($path);
			$this->assertStringNotContainsString('protected string $name;', $result);
			$this->assertStringContainsString('    protected int $id;', $result);
			$this->assertStringContainsString('    protected int $custom;', $result);
		} finally {
			unlink($path);
		}
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\EntityAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params): EntityAnnotator {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(EntityAnnotator::class)->onlyMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
