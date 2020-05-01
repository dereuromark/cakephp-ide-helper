<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use IdeHelper\Utility\AppPath;

class AppPathTest extends TestCase {

	/**
	 * @return void
	 */
	public function testGet() {
		$result = AppPath::get('View/Helper');
		$this->assertCount(1, $result);

		$path = array_shift($result);
		$this->assertTextContains('/tests/test_app/src/View/Helper/', $path);
	}

}
