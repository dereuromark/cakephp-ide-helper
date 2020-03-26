<?php

namespace IdeHelper\Test\TestCase\ValueObject;

use Cake\TestSuite\TestCase;
use IdeHelper\ValueObject\ClassName;

class ClassNameTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCreate(): void {
		$className = 'Foo\\Bar\\Baz';
		$result = ClassName::create($className);
		$this->assertSame($className, $result->raw());

		$result = ClassName::create('\\' . $className);
		$this->assertSame($className, $result->raw());
	}

	/**
	 * @return void
	 */
	public function testToString(): void {
		$className = 'Foo\\Bar\\Baz';
		$object = ClassName::create($className);

		$result = (string)$object;
		$this->assertSame('\\' . $className . '::class', $result);
	}

}
