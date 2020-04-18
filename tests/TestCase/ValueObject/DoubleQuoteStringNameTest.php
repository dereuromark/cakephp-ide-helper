<?php

namespace IdeHelper\Test\TestCase\ValueObject;

use Cake\TestSuite\TestCase;
use TestApp\ValueObject\DoubleQuoteStringName;

class DoubleQuoteStringNameTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCreate(): void {
		$stringName = 'Foo.Baz';
		$result = DoubleQuoteStringName::create($stringName);
		$this->assertSame($stringName, $result->raw());
	}

	/**
	 * @return void
	 */
	public function testToString(): void {
		$stringName = 'Foo.Baz';
		$object = DoubleQuoteStringName::create($stringName);

		$result = (string)$object;
		$this->assertSame('"' . $stringName . '"', $result);
	}

}
