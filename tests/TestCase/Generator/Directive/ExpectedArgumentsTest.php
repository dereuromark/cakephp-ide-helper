<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedArguments;
use Tools\TestSuite\TestCase;

class ExpectedArgumentsTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCollect() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$override = new ExpectedArguments('\\' . Table::class . '::addBehavior()', 0, $map);

		$result = (string)$override;
		$expected = <<<TXT
	expectedArguments(
		\\Cake\ORM\Table::addBehavior(),
		0,
		\\Foo\\Bar,
		"string"
	);
TXT;
		$this->assertSame($expected, $result);
	}

}
