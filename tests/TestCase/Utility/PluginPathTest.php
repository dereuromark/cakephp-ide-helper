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
		$this->assertSame(dirname(__DIR__, 3) . DS . 'vendor' . DS . 'dereuromark' . DS . 'cakephp-shim' . DS, $result);

		Plugin::getCollection()->remove('Awesome');

		$plugin = 'Awesome';
		$result = PluginPath::get($plugin);
		$this->assertSame(ROOT . DS . 'plugins' . DS . 'Awesome' . DS, $result);
	}

}
