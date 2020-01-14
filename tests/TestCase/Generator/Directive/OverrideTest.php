<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\Override;
use Shim\TestSuite\TestCase;

class OverrideTest extends TestCase {

	/**
	 * @return void
	 */
	public function testObject() {
		$map = [
			'Tree' => '\\' . Table::class,
			'CounterCache' => '\\' . Table::class,
		];
		$directive = new Override('\\' . Table::class . '::addBehavior(0)', $map);

		$result = $directive->build();
		$expected = <<<TXT
	override(
		\\Cake\ORM\Table::addBehavior(0),
		map([
			'Tree' => \Cake\ORM\Table,
			'CounterCache' => \Cake\ORM\Table,
		])
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . Table::class . '::addBehavior(0)@override', $directive->key());
	}

}
