<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedArguments;
use Shim\TestSuite\TestCase;

class ExpectedArgumentsTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$map = [
			'\Foo\Bar::class',
			'"string"',
		];
		$directive = new ExpectedArguments('\\' . Table::class . '::addBehavior()', 0, $map);

		$result = $directive->build();
		$expected = <<<'TXT'
	expectedArguments(
		\Cake\ORM\Table::addBehavior(),
		0,
		\Foo\Bar::class,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . Table::class . '::addBehavior()@0@expectedArguments', $directive->key());
	}

}
