<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use IdeHelper\Generator\PhpstormGenerator;
use IdeHelper\Generator\TaskCollection;
use Tools\TestSuite\TestCase;

class PhpstormGeneratorTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\PhpstormGenerator
	 */
	protected $generator;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$taskCollection = new TaskCollection();
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
		$result = $this->generator->generate();

		if (version_compare(Configure::version(), '3.6.0', '<')) {
			$file = Plugin::path('IdeHelper') . 'tests' . DS . 'test_files' . DS . 'meta' . DS . 'phpstorm' . DS . '.meta_legacy.php';
		} else {
			$file = Plugin::path('IdeHelper') . 'tests' . DS . 'test_files' . DS . 'meta' . DS . 'phpstorm' . DS . '.meta.php';
		}
		$expected = file_get_contents($file);

		$this->assertTextEquals($expected, $result);
	}

}
