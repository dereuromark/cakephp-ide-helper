<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Utility\Plugin;

class PluginTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Configure::delete('IdeHelper.plugins');
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		parent::tearDown();

		Configure::delete('IdeHelper.plugins');
	}

	/**
	 * @return void
	 */
	public function testAll() {
		$result = Plugin::all();
		$this->assertArrayHasKey('IdeHelper', $result);
		$this->assertArrayHasKey('Awesome', $result);
		$this->assertArrayHasKey('MyNamespace/MyPlugin', $result);
		$this->assertArrayNotHasKey('FooBar', $result);

		Configure::write('IdeHelper.plugins', ['FooBar', '-MyNamespace/MyPlugin']);

		$result = Plugin::all();
		$this->assertArrayHasKey('FooBar', $result);
		$this->assertArrayNotHasKey('MyNamespace/MyPlugin', $result);
	}

}
