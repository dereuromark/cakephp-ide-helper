<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use Tools\TestSuite\TestCase;

/**
 */
class AnnotationFactoryTest extends TestCase {

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @return void
	 */
	public function testCreate() {
		$annotation = AnnotationFactory::create('@method', '\\Foo\\Model\\Entity\\Bar', 'doSth()', 1);
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@property', '\\Foo\\Model\\Entity\\Bar', '$baz', 1);
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@foooo', '\\Foo', '$foo');
		$this->assertNull($annotation);
	}

	/**
	 * @return void
	 */
	public function testCreateFromString() {
		$annotation = AnnotationFactory::createFromString('@method \\Foo\\Model\\Entity\\Bar doSth($x, $y, $z)');
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);

		$annotation = AnnotationFactory::createFromString('@property \\Foo\\Model\\Entity\\Bar $baz');
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);

		$annotation = AnnotationFactory::createFromString('@property\\Foo\\Model\\Entity\\Bar$baz');
		$this->assertNull($annotation);
	}

}
