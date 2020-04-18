<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\ORM\Table;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\ValueObject\ClassName;
use Shim\TestSuite\TestCase;

class OverrideTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$map = [
			'Tree' => ClassName::create(Table::class),
			'CounterCache' => ClassName::create(Table::class),
		];
		$directive = new Override('\\' . Table::class . '::addBehavior(0)', $map);

		$result = $directive->build();
		$expected = <<<TXT
	override(
		\\Cake\ORM\Table::addBehavior(0),
		map([
			'Tree' => \Cake\ORM\Table::class,
			'CounterCache' => \Cake\ORM\Table::class,
		])
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . Table::class . '::addBehavior(0)@override', $directive->key());
	}

	/**
	 * @return void
	 */
	public function testBuildKeyNotEscaped() {
		$map = [
			'\\' . Table::class . '::class' => [
				'escapeKey' => false,
				'value' => '\\' . Table::class,
			],
		];
		$directive = new Override('\\' . Table::class . '::addBehavior(0)', $map);

		$result = $directive->build();
		$expected = <<<TXT
	override(
		\\Cake\ORM\Table::addBehavior(0),
		map([
			\Cake\ORM\Table::class => \Cake\ORM\Table,
		])
	);
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . Table::class . '::addBehavior(0)@override', $directive->key());
	}

}
