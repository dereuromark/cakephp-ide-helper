<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use IdeHelper\Utility\App;
use IdeHelper\View\Helper\DocBlockHelper;

class AppTest extends TestCase {

	/**
	 * @return void
	 */
	public function testClassName() {
		$result = App::className('Foo', 'Bar', 'Baz');
		$this->assertNull($result);

		$result = App::className('IdeHelper.DocBlock', 'View/Helper', 'Helper');
		$this->assertSame(DocBlockHelper::class, $result);
	}

}
