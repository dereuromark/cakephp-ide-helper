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
		Configure::delete('IdeHelper.tableEntityQuery');
		Configure::delete('IdeHelper.tableBehaviors');
		Configure::delete('IdeHelper.concreteEntitiesInParam');
		Configure::delete('IdeHelper.genericsInParam');
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

		// Force the legacy (no-entity-template) path so fixture-comparison tests stay
		// stable regardless of which CakePHP version is installed. The entity-template
		// path is exercised separately via _getEntityTemplateAnnotatorMock().
		$mock = $this->getMockBuilder(ModelAnnotator::class)
			->onlyMethods(['storeFile', 'supportsEntityTemplate', 'supportsEntityTemplateFindFamily'])
			->setConstructorArgs([$this->io, $params])
			->getMock();
		$mock->method('supportsEntityTemplate')->willReturn(false);
		$mock->method('supportsEntityTemplateFindFamily')->willReturn(false);

		return $mock;
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ModelAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getEntityTemplateAnnotatorMock(array $params): ModelAnnotator {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		$mock = $this->getMockBuilder(ModelAnnotator::class)
			->onlyMethods(['storeFile', 'supportsEntityTemplate', 'supportsEntityTemplateFindFamily'])
			->setConstructorArgs([$this->io, $params])
			->getMock();
		$mock->method('supportsEntityTemplate')->willReturn(true);
		$mock->method('supportsEntityTemplateFindFamily')->willReturn(true);

		return $mock;
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\ModelAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getEntityTemplateWithoutFindFamilyAnnotatorMock(array $params): ModelAnnotator {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		$mock = $this->getMockBuilder(ModelAnnotator::class)
			->onlyMethods(['storeFile', 'supportsEntityTemplate', 'supportsEntityTemplateFindFamily'])
			->setConstructorArgs([$this->io, $params])
			->getMock();
		$mock->method('supportsEntityTemplate')->willReturn(true);
		$mock->method('supportsEntityTemplateFindFamily')->willReturn(false);

		return $mock;
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
		$annotator->method('storeFile')
			->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/BarBarsAbstractTable.php';
		$annotator->annotate($path);

		$output = $this->out->output();
		$this->assertTextContains('  -> 18 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateDetailed() {
		Configure::write('IdeHelper.genericsInParam', 'detailed');
		Configure::write('IdeHelper.arrayAsGenerics', true);
		Configure::write('IdeHelper.objectAsGenerics', true);

		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/BarBarsDetailedTable.php'));
		$callback = function ($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = APP . 'Model/Table/BarBarsTable.php';
		$annotator->annotate($path);

		Configure::delete('IdeHelper.genericsInParam');
		Configure::delete('IdeHelper.arrayAsGenerics');
		Configure::delete('IdeHelper.objectAsGenerics');

		$output = $this->out->output();
		$this->assertTextContains('  -> 18 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithEntityFindQuery() {
		Configure::write('IdeHelper.tableEntityQuery', true);

		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/BarBarsTable.php'));
		$expectedContent = str_replace(
			" * @method \\TestApp\\Model\\Entity\\BarBar get(mixed \$primaryKey, array|string \$finder = 'all', \\Psr\\SimpleCache\\CacheInterface|string|null \$cache = null, \\Closure|string|null \$cacheKey = null, mixed ...\$args)\n * @method \\TestApp\\Model\\Entity\\BarBar findOrCreate(\\Cake\\ORM\\Query\\SelectQuery|callable|array \$search, ?callable \$callback = null, array \$options = [])",
			" * @method \\TestApp\\Model\\Entity\\BarBar get(mixed \$primaryKey, array|string \$finder = 'all', \\Psr\\SimpleCache\\CacheInterface|string|null \$cache = null, \\Closure|string|null \$cacheKey = null, mixed ...\$args)\n * @method \\Cake\\ORM\\Query\\SelectQuery<\\TestApp\\Model\\Entity\\BarBar> find(string \$type = 'all', mixed ...\$args)\n * @method \\TestApp\\Model\\Entity\\BarBar findOrCreate(\\Cake\\ORM\\Query\\SelectQuery|callable|array \$search, ?callable \$callback = null, array \$options = [])",
			$expectedContent,
		);

		$callback = function ($value) use ($expectedContent) {
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
		$this->assertTextContains('  -> 19 annotations added', $output);
	}

	/**
	 * @return void
	 */
	public function testAnnotateWithEntityTemplate() {
		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'Model/Table/BarBarsEntityTemplateTable.php'));
		$callback = function ($value) use ($expectedContent) {
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
		$this->assertTextContains('  -> 9 annotations added', $output);
	}

	/**
	 * Regression: even when CakePHP supports `TEntity`, the @method overrides must
	 * still be emitted if `IdeHelper.tableBehaviors` excludes `extends`. Without an
	 * `@extends Table<..., TEntity>` annotation there is no parent generic for PHPStan
	 * to resolve from, so suppressing the overrides would lose the concrete types.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsOverridesWhenExtendsDisabled() {
		Configure::write('IdeHelper.tableBehaviors', 'mixin');

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('save(', $capture);
		$this->assertStringContainsString('saveOrFail(', $capture);
		$this->assertStringContainsString('newEmptyEntity()', $capture);
		$this->assertStringContainsString(' get(mixed $primaryKey', $capture);
		$this->assertStringContainsString('findOrCreate(', $capture);
		$this->assertStringContainsString('newEntity(', $capture);
		$this->assertStringContainsString('newEntities(', $capture);
		$this->assertStringContainsString('patchEntity(', $capture);
		$this->assertStringContainsString('patchEntities(', $capture);
	}

	/**
	 * Regression: when the immediate parent is a custom base table (not `\Cake\ORM\Table`),
	 * we cannot assume the parent re-declares the entity template, so the @method
	 * overrides must still be emitted even on CakePHP 5.4+.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsOverridesForCustomParent() {
		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsAbstractTable.php');

		$this->assertStringContainsString('save(', $capture);
		$this->assertStringContainsString('saveOrFail(', $capture);
		$this->assertStringContainsString('newEmptyEntity()', $capture);
		$this->assertStringContainsString(' get(mixed $primaryKey', $capture);
		$this->assertStringContainsString('findOrCreate(', $capture);
		$this->assertStringContainsString('newEntity(', $capture);
		$this->assertStringContainsString('patchEntity(', $capture);
	}

	/**
	 * Regression: in `concreteEntitiesInParam` mode the `save()` and `saveOrFail()`
	 * overrides narrow `$entity` to the concrete entity class, which the parent's
	 * `EntityInterface` param does not provide. Keep the overrides when this
	 * narrowing is requested.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsSaveOverridesWhenConcreteEntitiesInParam() {
		Configure::write('IdeHelper.concreteEntitiesInParam', true);

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('save(\TestApp\Model\Entity\BarBar $entity', $capture);
		$this->assertStringContainsString('saveOrFail(\TestApp\Model\Entity\BarBar $entity', $capture);
		// `patchEntity()` and `patchEntities()` are also kept under `concreteEntitiesInParam`.
		$this->assertStringContainsString('patchEntity(\TestApp\Model\Entity\BarBar $entity', $capture);
		$this->assertStringContainsString('patchEntities(', $capture);
		// `newEmptyEntity` remains suppressed because it has no parameters to narrow.
		$this->assertStringNotContainsString('newEmptyEntity()', $capture);
		// `newEntity` / `newEntities` aren't entity-typed in their parameters, so they
		// remain suppressed in non-detailed mode.
		$this->assertStringNotContainsString('newEntity(', $capture);
	}

	/**
	 * Regression: in `genericsInParam=detailed` mode the `get()` override narrows
	 * `$finder` to `array<string, mixed>|string`, narrower than the parent's
	 * `array|string`. Keep the override when this narrowing is requested.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsGetWhenDetailedGenerics() {
		Configure::write('IdeHelper.genericsInParam', 'detailed');

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('get(mixed $primaryKey, array<string, mixed>|string', $capture);
		// detailed mode also narrows `$search` on `findOrCreate()`, so its override stays.
		$this->assertStringContainsString('findOrCreate(\Cake\ORM\Query\SelectQuery<\TestApp\Model\Entity\BarBar>|callable|array<string, mixed>', $capture);
		// detailed mode narrows `$data` on the marshalling family, so their overrides stay.
		$this->assertStringContainsString('newEntity(array<string, mixed> $data', $capture);
		$this->assertStringContainsString('newEntities(array<array<string, mixed>> $data', $capture);
		$this->assertStringContainsString('patchEntity(', $capture);
		$this->assertStringContainsString('patchEntities(', $capture);
	}

	/**
	 * `IdeHelper.tableEntityQuery` historically emits a `find()` @method override
	 * narrowed to `SelectQuery<Entity>`. With the entity-template emitted, the
	 * parent's `find()` already returns `SelectQuery<TEntity|array>` — so we drop
	 * the override on Cake 5.4+ (the `|array` fallback is actually more truthful
	 * since `find()` can be hydration-disabled).
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateSuppressesTableEntityQueryFindOverride() {
		Configure::write('IdeHelper.tableEntityQuery', true);

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringNotContainsString('SelectQuery<\TestApp\Model\Entity\BarBar> find(', $capture);
	}

	/**
	 * Regression: when the entity-template guards do NOT hold (e.g. `tableBehaviors=mixin`
	 * so no @extends is emitted), the `find()` override under `tableEntityQuery` must
	 * still be emitted — the parent generic isn't available to resolve from.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsTableEntityQueryFindOverrideWhenExtendsDisabled() {
		Configure::write('IdeHelper.tableEntityQuery', true);
		Configure::write('IdeHelper.tableBehaviors', 'mixin');

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('SelectQuery<\TestApp\Model\Entity\BarBar> find(', $capture);
	}

	/**
	 * `loadInto()` is only emitted in `concreteEntitiesInParam=strict` mode. With
	 * the entity-template emitted, the parent's `loadInto()` already declares
	 * `TEntity|array<TEntity>` for both param and return, so the override is
	 * redundant on Cake 5.4+ (after cakephp/cakephp#19438).
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateSuppressesStrictLoadIntoOverride() {
		Configure::write('IdeHelper.concreteEntitiesInParam', 'strict');

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringNotContainsString('loadInto(', $capture);
	}

	/**
	 * Regression: on CakePHP versions that have the entity template (5.3.4+) but
	 * NOT the find-family TEntity propagation (pre-#19438), the find/findOrCreate/
	 * loadInto overrides must still be emitted even though the entity template
	 * is otherwise present. The other entity-returning methods (`get`/`saveOrFail`
	 * etc.) remain suppressed because their TEntity propagation landed earlier.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsFindAndFindOrCreateWithoutFindFamilySupport() {
		Configure::write('IdeHelper.tableEntityQuery', true);

		$annotator = $this->_getEntityTemplateWithoutFindFamilyAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('SelectQuery<\TestApp\Model\Entity\BarBar> find(', $capture);
		$this->assertStringContainsString('findOrCreate(', $capture);
		// Non-find-family methods are still suppressed because the entity template
		// itself IS present and their parent annotations already carry TEntity.
		$this->assertStringNotContainsString('newEmptyEntity()', $capture);
		$this->assertStringNotContainsString('saveOrFail(', $capture);
	}

	/**
	 * Mirrors the previous test for `loadInto()`, which only emits in strict mode.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsStrictLoadIntoWithoutFindFamilySupport() {
		Configure::write('IdeHelper.concreteEntitiesInParam', 'strict');

		$annotator = $this->_getEntityTemplateWithoutFindFamilyAnnotatorMock([]);

		$capture = '';
		$annotator->expects($this->once())
			->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsTable.php');

		$this->assertStringContainsString('loadInto(', $capture);
	}

	/**
	 * Regression: in strict mode without the entity-template guards (e.g. on a
	 * custom-parent table), the `loadInto()` override is still needed.
	 *
	 * @return void
	 */
	public function testAnnotateWithEntityTemplateKeepsStrictLoadIntoOverrideForCustomParent() {
		Configure::write('IdeHelper.concreteEntitiesInParam', 'strict');

		$annotator = $this->_getEntityTemplateAnnotatorMock([]);

		$capture = '';
		$annotator->method('storeFile')
			->with($this->anything(), $this->callback(function ($value) use (&$capture) {
				$capture = $value;

				return true;
			}));

		$annotator->annotate(APP . 'Model/Table/BarBarsAbstractTable.php');

		$this->assertStringContainsString('loadInto(', $capture);
	}

}
