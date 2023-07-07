<?php

namespace IdeHelper\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use IdeHelper\Command\AnnotateCommand;

class AnnotateCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	protected array $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
		'plugin.IdeHelper.Houses',
		'plugin.IdeHelper.Windows',
	];

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}
	}

	/**
	 * @return void
	 */
	public function testModels(): void {
		$this->exec('annotate models -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotations added');
	}

	/**
	 * @return void
	 */
	public function testViews(): void {
		$this->exec('annotate view -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotations added');
	}

	/**
	 * @return void
	 */
	public function testHelpers(): void {
		$this->exec('annotate helpers -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotations added');
	}

	/**
	 * @return void
	 */
	public function testTemplates(): void {
		$this->exec('annotate templates -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotations added');
	}

	/**
	 * @return void
	 */
	public function testControllers() {
		$this->exec('annotate controllers -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains('BarController');
		$this->assertOutputContains(' annotations added');
		$this->assertOutputContains('FooController');
	}

	/**
	 * @return void
	 */
	public function testCommands(): void {
		$this->exec('annotate commands -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotation added');
	}

	/**
	 * @return void
	 */
	public function testRoutes(): void {
		$this->exec('annotate routes -d -v -r');
		$this->assertExitSuccess();
	}

	/**
	 * @return void
	 */
	public function testClasses(): void {
		$this->exec('annotate classes -d -v');
		$this->assertExitSuccess();
		$this->assertOutputContains(' annotation added');
	}

	/**
	 * @return void
	 */
	public function testCallbacks(): void {
		$this->exec('annotate callbacks -d -v');
		$this->assertExitSuccess();
	}

	/**
	 * @return void
	 */
	public function testAll() {
		$this->exec('annotate all -d -v -r');
		$this->assertExitSuccess();

		$this->assertOutputContains('[Models]');
		$this->assertOutputContains('[Controllers]');
		$this->assertOutputContains('[View]');
		$this->assertOutputContains('[Templates]');
		$this->assertOutputContains('[Commands]');
		$this->assertOutputContains('[Components]');
		$this->assertOutputContains('[Helpers]');
	}

	/**
	 * @return void
	 */
	public function testAllCiModeNoChanges() {
		$this->exec('annotate all -d -v --ci -p Awesome');
		$this->assertExitSuccess();
	}

	/**
	 * @return void
	 */
	public function testAllCiModeChanges() {
		$this->exec('annotate all -d -v --ci');

		$this->assertExitCode(AnnotateCommand::CODE_CHANGES);
	}

	/**
	 * @return array
	 */
	public function provideSubcommandsForCiModeTest() {
		return [
			'models' => ['models'],
			'view' => ['view'],
			'helpers' => ['helpers'],
			'components' => ['components'],
			'templates' => ['templates'],
			'controllers' => ['controllers'],
		];
	}

	/**
	 * @dataProvider provideSubcommandsForCiModeTest
	 *
	 * @param string $subcommand The subcommand to be tested
	 * @return void
	 */
	public function testIndividualSubcommandCiModeNoChanges($subcommand) {
		$this->skipIf($subcommand === 'view', 'View does not support the plugin parameter');

		$this->exec('annotate ' . $subcommand . ' -d -v --ci -p Awesome');
		$this->assertExitSuccess();
	}

	/**
	 * @dataProvider provideSubcommandsForCiModeTest
	 *
	 * @param string $subcommand The subcommand to be tested
	 * @return void
	 */
	public function testIndividualSubcommandCiModeChanges($subcommand) {
		$this->exec('annotate ' . $subcommand . ' -d -v --ci');

		$this->assertExitCode(AnnotateCommand::CODE_CHANGES);
	}

}
