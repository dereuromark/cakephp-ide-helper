<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedReturnValues;
use Shim\TestSuite\TestCase;

class ExpectedReturnValuesTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$map = [
			'\Foo\Bar::class',
			'"string"',
		];
		$directive = new ExpectedReturnValues('\\' . Table::class . '::addBehavior()', $map);

		$result = $directive->build();
		$expected = <<<'TXT'
	expectedReturnValues(
		\Cake\ORM\Table::addBehavior(),
		\Foo\Bar::class,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . Table::class . '::addBehavior()@expectedReturnValues', $directive->key());
	}

}
