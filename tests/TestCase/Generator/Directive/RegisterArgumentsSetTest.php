<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\ExpectedReturnValues;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use Shim\TestSuite\TestCase;

class RegisterArgumentsSetTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$list = [
			'\\Foo\\Bar',
			'"string"',
		];
		$directive = new RegisterArgumentsSet('foo', $list);

		$result = $directive->build();
		$expected = <<<TXT
	registerArgumentsSet(
		'foo',
		\\Foo\\Bar,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('foo@registerArgumentsSet', $directive->key());
	}

	/**
	 * @return void
	 */
	public function testToString() {
		$list = [
			'\\Foo\\Bar',
		];
		$directive = new RegisterArgumentsSet('fooBar', $list);

		$result = (string)$directive;
		$this->assertSame('argumentsSet(\'fooBar\')', $result);
	}

	/**
	 * @return void
	 */
	public function testSetInsideArguments() {
		$list = [
			'\\Foo\\Bar',
			'"string"',
		];
		$argumentsSet = new RegisterArgumentsSet('fooBar', $list);

		$list = [
			$argumentsSet,
		];
		$directive = new ExpectedArguments('\\My\\Class::someMethod()', 1, $list);

		$result = $directive->build();
		$expected = <<<TXT
	expectedArguments(
		\\My\\Class::someMethod(),
		1,
		argumentsSet('fooBar')
	);
TXT;
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testArgumentsSetInsideReturnValues() {
		$list = [
			'\\Foo\\Bar',
			'"string"',
		];
		$argumentsSet = new RegisterArgumentsSet('fooBar', $list);

		$list = [
			$argumentsSet,
		];
		$directive = new ExpectedReturnValues('\\My\\Class::someMethod()', $list);

		$result = $directive->build();
		$expected = <<<TXT
	expectedReturnValues(
		\\My\\Class::someMethod(),
		argumentsSet('fooBar')
	);
TXT;
		$this->assertSame($expected, $result);
	}

}
