<?php

namespace IdeHelper\Test\TestCase\ValueObject;

use Cake\TestSuite\TestCase;
use IdeHelper\ValueObject\StringName;

class StringNameTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCreate() {
		$stringName = 'Foo.Baz';
		$result = StringName::create($stringName);
		$this->assertSame($stringName, $result->raw());
	}

	/**
	 * @return void
	 */
	public function testToString() {
		$stringName = 'Foo.Baz';
		$object = StringName::create($stringName);

		$result = (string)$object;
		$this->assertSame('\'' . $stringName . '\'', $result);
	}

}
