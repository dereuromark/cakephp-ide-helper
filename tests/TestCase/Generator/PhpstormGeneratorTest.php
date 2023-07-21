<?php

namespace IdeHelper\Test\TestCase\Generator;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\Task\EnvTask;
use IdeHelper\Generator\Task\FixtureTask;
use IdeHelper\Generator\TaskCollection;
use Shim\TestSuite\TestCase;
use TestApp\Generator\Task\TestEnvTask;
use TestApp\Generator\Task\TestFixtureTask;

class PhpstormGeneratorTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	protected ?PhpstormGenerator $generator = null;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$taskCollection = new TaskCollection([
			EnvTask::class => TestEnvTask::class,
			FixtureTask::class => TestFixtureTask::class,
		]);
		$this->generator = new PhpstormGenerator($taskCollection);

		$file = TMP . '.meta.php';
		if (file_exists($file)) {
			unlink($file);
		}
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();

		$file = TMP . '.meta.php';
		if (file_exists($file)) {
			unlink($file);
		}
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		Configure::write('IdeHelper.skipDatabaseTables', ['/^(?!wheels)/']);

		$result = $this->generator->generate();
		file_put_contents(TMP . '.meta.php', $result);

		$file = Plugin::path('IdeHelper') . 'tests' . DS . 'test_files' . DS . 'meta' . DS . 'phpstorm' . DS . '.meta.php';
		$expected = file_get_contents($file);

		$this->assertTextEquals($expected, $result);
	}

}
