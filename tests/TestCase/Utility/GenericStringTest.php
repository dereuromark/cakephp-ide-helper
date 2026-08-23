<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\TestSuite\TestCase;
use IdeHelper\Utility\GenericString;

class GenericStringTest extends TestCase {

	/**
	 * @return void
	 */
	public function testClassName() {
		$result = GenericString::generate('Foo');
		$this->assertSame('Foo[]', $result);

		Configure::write('IdeHelper.arrayAsGenerics', true);

		$result = GenericString::generate('Foo');

		Configure::delete('IdeHelper.arrayAsGenerics');

		$this->assertSame('array<Foo>', $result);
	}

	/**
	 * @return void
	 */
	public function testClassNameObject() {
		$result = GenericString::generate('\Foo', '\Bar');
		$this->assertSame('\Foo[]|\Bar', $result);

		Configure::write('IdeHelper.objectAsGenerics', true);

		$result = GenericString::generate('\Foo', '\Bar');

		Configure::delete('IdeHelper.objectAsGenerics');

		$this->assertSame('\Bar<\Foo>', $result);
	}

	/**
	 * A ResultSetInterface always emits both template params (TKey, TValue). The generic form
	 * must be produced whenever generics are enabled for objects (`objectAsGenerics`) or params
	 * (`genericsInParam` true|'detailed'), or when concrete entities are requested - not only for
	 * 'detailed'. Otherwise the documented `ResultSetInterface<int, TEntity>` return type silently
	 * regressed to the `Foo[]|...` union despite generics being on.
	 *
	 * @return void
	 */
	public function testClassNameResultSetInterface() {
		$type = '\\' . ResultSetInterface::class;

		// Legacy fallback (no generics enabled): union, but still both template params.
		$result = GenericString::generate('\Foo', $type);
		$this->assertSame('\Foo[]|' . $type . '<int, \Foo>', $result);

		$enablingConfigs = [
			['objectAsGenerics', true],
			['genericsInParam', true],
			['genericsInParam', 'detailed'],
			['concreteEntitiesInParam', true],
		];
		foreach ($enablingConfigs as [$key, $value]) {
			Configure::write('IdeHelper.' . $key, $value);
			$result = GenericString::generate('\Foo', $type);
			Configure::delete('IdeHelper.' . $key);

			$this->assertSame($type . '<int, \Foo>', $result, "Failed for IdeHelper.$key=" . var_export($value, true));
		}
	}

	/**
	 * CollectionInterface declares both key and value template parameters in CakePHP 5.4.
	 *
	 * @return void
	 */
	public function testClassNameCollectionInterface() {
		$type = '\\' . CollectionInterface::class;

		$result = GenericString::generate('\\Foo', $type);
		$this->assertSame('\\Foo[]|' . $type . '<int, \\Foo>', $result);

		Configure::write('IdeHelper.objectAsGenerics', true);
		$result = GenericString::generate('\\Foo', $type);
		Configure::delete('IdeHelper.objectAsGenerics');

		$this->assertSame($type . '<int, \\Foo>', $result);
	}

	/**
	 * PaginatedInterface is the return type of Controller::paginate() and declares the same two template
	 * params (TKey, TValue) as ResultSetInterface, so it must be emitted the same way.
	 *
	 * @return void
	 */
	public function testClassNamePaginatedInterface() {
		$type = '\\' . PaginatedInterface::class;

		// Legacy fallback (no generics enabled): union, but still both template params.
		$result = GenericString::generate('\Foo', $type);
		$this->assertSame('\Foo[]|' . $type . '<int, \Foo>', $result);

		$enablingConfigs = [
			['objectAsGenerics', true],
			['genericsInParam', true],
			['genericsInParam', 'detailed'],
			['concreteEntitiesInParam', true],
		];
		foreach ($enablingConfigs as [$key, $value]) {
			Configure::write('IdeHelper.' . $key, $value);
			$result = GenericString::generate('\Foo', $type);
			Configure::delete('IdeHelper.' . $key);

			$this->assertSame($type . '<int, \Foo>', $result, "Failed for IdeHelper.$key=" . var_export($value, true));
		}
	}

}
