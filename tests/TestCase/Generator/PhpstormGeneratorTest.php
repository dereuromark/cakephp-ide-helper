<?php

namespace IdeHelper\Test\TestCase\Generator;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\Task\EnvTask;
use IdeHelper\Generator\TaskCollection;
use Shim\TestSuite\TestCase;
use TestApp\Generator\Task\TestEnvTask;

class PhpstormGeneratorTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\PhpstormGenerator
	 */
	protected $generator;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$taskCollection = new TaskCollection([
			EnvTask::class => TestEnvTask::class,
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
	public function testCollect() {
		Configure::write('IdeHelper.skipDatabaseTables', ['/^(?!wheels)/']);

		$result = $this->generator->generate();
		if ($this->isDebug()) {
			file_put_contents(TMP . '.meta.php', $result);
		}

		$file = Plugin::path('IdeHelper') . 'tests' . DS . 'test_files' . DS . 'meta' . DS . 'phpstorm' . DS . '.meta.php';
		$expected = file_get_contents($file);

		$this->assertTextEquals($expected, $result);
	}

}
