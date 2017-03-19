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
	}

}
