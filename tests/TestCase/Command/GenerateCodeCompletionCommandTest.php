<?php

namespace IdeHelper\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class GenerateCodeCompletionCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	protected array $files = [
		TMP . 'CodeCompletionCakeController.php',
		TMP . 'CodeCompletionCakeORM.php',
		TMP . 'CodeCompletionCakeView.php',
	];

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		foreach ($this->files as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();

		foreach ($this->files as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}
	}

	/**
	 * @return void
	 */
	public function testGenerate() {
		$this->exec('code_completion generate');
		$this->assertOutputContains('CodeCompletion files generated: Cake\Controller, Cake\ORM, Cake\View');
		$this->assertExitSuccess();
	}

}
