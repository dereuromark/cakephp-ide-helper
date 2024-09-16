<?php

namespace IdeHelper\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class IlluminateCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function testIlluminateDryRun() {
		$this->loadPlugins(['IdeHelper']);
		$this->exec('illuminate -d -v');

		$this->assertExitSuccess();
		$this->assertOutputContains('# /src/Illuminator/Illuminator.php');
	}

}
