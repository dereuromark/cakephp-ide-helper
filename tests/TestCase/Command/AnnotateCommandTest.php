<?php

namespace IdeHelper\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
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
		$this->loadPlugins(['IdeHelper']);

		Configure::write('IdeHelper.assocsAsGenerics', true);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		Configure::delete('IdeHelper.assocsAsGenerics');
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
	public function testTemplatesSkipFiles(): void {
		$this->exec('annotate templates -d -v --file templates/element/deeply/nested.php');
		$this->assertExitSuccess();
		$this->assertOutputContains('-> nested');
		$this->assertOutputNotContains('-> example');
	}

	/**
	 * @return void
	 */
	public function testControllers() {
		$this->exec('annotate controllers -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains('BarController');
		$this->assertOutputContains(' annotations added');
		$this->assertOutputContains('FoosController');
	}

	/**
	 * @return void
	 */
	public function testCommands(): void {
		$this->exec('annotate commands -d -v -r');
		$this->assertExitSuccess();
		$this->assertOutputContains(' 2 annotations added');
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
		$this->assertExitSuccess($this->_out->output());
	}

	/**
	 * @return void
	 */
	public function testAllCiModeChanges() {
		$this->exec('annotate all -d -v --ci');

		$this->assertExitCode(AnnotateCommand::CODE_CHANGES, $this->_out->output());
	}

	/**
	 * @return array
	 */
	public static function provideSubcommandsForCiModeTest() {
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
	 *
	 * @param string $subcommand The subcommand to be tested
	 * @return void
	 */
	#[\PHPUnit\Framework\Attributes\DataProvider('provideSubcommandsForCiModeTest')]
	public function testIndividualSubcommandCiModeNoChanges(string $subcommand): void {
		$this->skipIf($subcommand === 'view', 'View does not support the plugin parameter');

		$this->exec('annotate ' . $subcommand . ' -d -v --ci -p Awesome');
		$this->assertExitSuccess($this->_out->output());
	}

	/**
	 *
	 * @param string $subcommand The subcommand to be tested
	 * @return void
	 */
	#[\PHPUnit\Framework\Attributes\DataProvider('provideSubcommandsForCiModeTest')]
	public function testIndividualSubcommandCiModeChanges(string $subcommand): void {
		$this->exec('annotate ' . $subcommand . ' -d -v --ci');

		$this->assertExitCode(AnnotateCommand::CODE_CHANGES, $this->_out->output());
	}

}
