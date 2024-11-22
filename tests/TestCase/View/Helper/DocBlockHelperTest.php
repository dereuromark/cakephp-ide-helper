<?php
declare(strict_types=1);

namespace IdeHelper\Test\TestCase\View\Helper;

use Bake\View\BakeView;
use Cake\Http\Response;
use Cake\Http\ServerRequest as Request;
use Cake\TestSuite\TestCase;
use IdeHelper\View\Helper\DocBlockHelper;

/**
 * DocBlockHelper Test
 */
#[\PHPUnit\Framework\Attributes\CoversClass(DocBlockHelper::class)]
class DocBlockHelperTest extends TestCase {

	/**
	 * @var \IdeHelper\View\Helper\DocBlockHelper
	 */
	protected $DocBlockHelper;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$request = new Request();
		$response = new Response();
		$View = new BakeView($request, $response);
		$this->DocBlockHelper = new DocBlockHelper($View);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
		unset($this->DocBlockHelper);
	}

	/**
	 * Tests the classDescription method including annotation spacing
	 *
	 * @return void
	 */
	public function testBuildTableAnnotations(): void {
		$associations = [];
		$associationInfo = [
			'BelongsTo' => [
				'User' => [
					'className' => 'Users',
					'foreignKey' => 'user_id',
				],
			],
		];
		$behaviors = [];
		$entity = 'Foo';
		$namespace = 'Bar';

		$result = $this->DocBlockHelper->buildTableAnnotations($associations, $associationInfo, $behaviors, $entity, $namespace);

		$expected = [
			'@method \\Bar\\Model\\Entity\\Foo newEmptyEntity()',
			'@method \\Bar\\Model\\Entity\\Foo newEntity(array $data, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[] newEntities(array $data, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo get(mixed $primaryKey, array|string $finder = \'all\', \\Psr\\SimpleCache\\CacheInterface|string|null $cache = null, \\Closure|string|null $cacheKey = null, mixed ...$args)',
			'@method \\Bar\\Model\\Entity\\Foo findOrCreate(\\Cake\\ORM\\Query\\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo patchEntity(\\Cake\\Datasource\\EntityInterface $entity, array $data, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[] patchEntities(iterable $entities, array $data, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo|false save(\\Cake\\Datasource\\EntityInterface $entity, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo saveOrFail(\\Cake\\Datasource\\EntityInterface $entity, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[]|\\Cake\\Datasource\\ResultSetInterface<\\Bar\\Model\\Entity\\Foo>|false saveMany(iterable $entities, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[]|\\Cake\\Datasource\\ResultSetInterface<\\Bar\\Model\\Entity\\Foo> saveManyOrFail(iterable $entities, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[]|\\Cake\\Datasource\\ResultSetInterface<\\Bar\\Model\\Entity\\Foo>|false deleteMany(iterable $entities, array $options = [])',
			'@method \\Bar\\Model\\Entity\\Foo[]|\\Cake\\Datasource\\ResultSetInterface<\\Bar\\Model\\Entity\\Foo> deleteManyOrFail(iterable $entities, array $options = [])',
		];
		$this->assertEquals($expected, $result);
	}

}
