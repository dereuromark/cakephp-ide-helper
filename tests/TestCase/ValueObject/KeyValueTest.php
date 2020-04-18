<?php

namespace IdeHelper\Test\TestCase\ValueObject;

use Cake\TestSuite\TestCase;
use IdeHelper\ValueObject\ClassName;
use IdeHelper\ValueObject\KeyValue;

class KeyValueTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCreate(): void {
		$key = ClassName::create(KeyValue::class);

		$value = ClassName::create(KeyValue::class);
		$result = KeyValue::create($key, $value);

		$this->assertSame($key, $result->key());
		$this->assertSame($value, $result->value());
	}

}
