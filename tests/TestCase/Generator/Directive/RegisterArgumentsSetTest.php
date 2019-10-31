<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\ExpectedReturnValues;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use Tools\TestSuite\TestCase;

class RegisterArgumentsSetTest extends TestCase {

	/**
	 * @return void
	 */
	public function testObject() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$directive = new RegisterArgumentsSet('foo', $map);

		$result = $directive->build();
		$expected = <<<TXT
	registerArgumentsSet(
		'foo',
		\\Foo\\Bar,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testToString() {
		$map = [
			'\\Foo\\Bar',
		];
		$directive = new RegisterArgumentsSet('fooBar', $map);

		$result = (string)$directive;
		$this->assertSame('argumentsSet("fooBar")', $result);
	}

	/**
	 * @return void
	 */
	public function testSetInsideArguments() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$argumentsSet = new RegisterArgumentsSet('fooBar', $map);

		$map = [
			$argumentsSet,
		];
		$directive = new ExpectedArguments('\\My\\Class::someMethod()', 1, $map);

		$result = $directive->build();
		$expected = <<<TXT
	expectedArguments(
		\\My\\Class::someMethod(),
		1,
		argumentsSet("fooBar")
	);
TXT;
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testArgumentsSetInsideReturnValues() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$argumentsSet = new RegisterArgumentsSet('fooBar', $map);

		$map = [
			$argumentsSet,
		];
		$directive = new ExpectedReturnValues('\\My\\Class::someMethod()', $map);

		$result = $directive->build();
		$expected = <<<TXT
	expectedReturnValues(
		\\My\\Class::someMethod(),
		argumentsSet("fooBar")
	);
TXT;
		$this->assertSame($expected, $result);
	}

}
