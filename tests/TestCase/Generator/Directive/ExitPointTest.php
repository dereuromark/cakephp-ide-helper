<?php

namespace IdeHelper\Test\TestCase\Generator\Directive;

use Cake\Console\ConsoleIo;
use IdeHelper\Generator\Directive\ExitPoint;
use Shim\TestSuite\TestCase;

class ExitPointTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$directive = new ExitPoint('\\' . ConsoleIo::class . '::abort()');

		$result = $directive->build();
		$expected = <<<'TXT'
	exitPoint(\Cake\Console\ConsoleIo::abort());
TXT;
		$this->assertSame($expected, $result);
		$this->assertSame('\\' . ConsoleIo::class . '::abort()@exitPoint', $directive->key());
	}

}
