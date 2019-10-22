<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use Tools\TestSuite\TestCase;

class RegisterArgumentsSetTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCollect() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$override = new RegisterArgumentsSet('foo', $map);

		$result = (string)$override;
		$expected = <<<TXT
	registerArgumentsSet(
		'foo',
		\\Foo\\Bar,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
	}

}
