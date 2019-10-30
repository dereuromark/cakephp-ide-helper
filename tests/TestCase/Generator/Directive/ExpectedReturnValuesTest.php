<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedReturnValues;
use Tools\TestSuite\TestCase;

class ExpectedReturnValuesTest extends TestCase {

	/**
	 * @return void
	 */
	public function testObject() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$directive = new ExpectedReturnValues('\\' . Table::class . '::addBehavior()', $map);

		$result = $directive->build();
		$expected = <<<TXT
	expectedReturnValues(
		\\Cake\ORM\Table::addBehavior(),
		\\Foo\\Bar,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
	}

}
