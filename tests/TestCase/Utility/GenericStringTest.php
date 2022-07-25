<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Utility\GenericString;

class GenericStringTest extends TestCase {

	/**
	 * @return void
	 */
	public function testClassName() {
		$result = GenericString::generate('Foo');
		$this->assertSame('Foo[]', $result);

		Configure::write('IdeHelper.arrayAsGenerics', true);

		$result = GenericString::generate('Foo');

		Configure::delete('IdeHelper.arrayAsGenerics');

		$this->assertSame('array<Foo>', $result);
	}

	/**
	 * @return void
	 */
	public function testClassNameObject() {
		$result = GenericString::generate('\Foo', '\Bar');
		$this->assertSame('\Foo[]|\Bar', $result);

		Configure::write('IdeHelper.objectAsGenerics', true);

		$result = GenericString::generate('\Foo', '\Bar');

		Configure::delete('IdeHelper.objectAsGenerics');

		$this->assertSame('\Bar<\Foo>', $result);
	}

}
