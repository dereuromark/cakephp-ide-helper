<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use IdeHelper\Utility\PluginPath;

class PluginPathTest extends TestCase {

	/**
	 * @return void
	 */
	public function testGet() {
		$plugin = 'Shim';
		$result = PluginPath::get($plugin);
		$this->assertTextContains('cakephp-ide-helper/vendor/dereuromark/cakephp-shim/', $result);

		Plugin::getCollection()->remove('Awesome');

		$plugin = 'Awesome';
		$result = PluginPath::get($plugin);
		$this->assertTextContains('cakephp-ide-helper/tests/test_app/plugins/Awesome/', $result);
	}

}
