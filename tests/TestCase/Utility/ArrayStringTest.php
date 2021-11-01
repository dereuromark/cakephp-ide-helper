<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Utility\ArrayString;

class ArrayStringTest extends TestCase {

	/**
	 * @return void
	 */
	public function testClassName() {
		$result = ArrayString::generate('Foo');
		$this->assertSame('Foo[]', $result);

		Configure::write('IdeHelper.arrayAsGenerics', true);

		$result = ArrayString::generate('Foo');

		Configure::delete('IdeHelper.arrayAsGenerics');

		$this->assertSame('array<Foo>', $result);
	}

}
