<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\ExpectedReturnValues;
use Tools\TestSuite\TestCase;

class ExpectedReturnValuesTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCollect() {
		$map = [
			'\\Foo\\Bar',
			'"string"',
		];
		$override = new ExpectedReturnValues('\\' . Table::class . '::addBehavior()', $map);

		$result = (string)$override;
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
