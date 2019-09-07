<?php

namespace IdeHelper\Test\TestCase\Annotation;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotation\PropertyReadAnnotation;
use IdeHelper\Annotation\UsesAnnotation;

class AnnotationFactoryTest extends TestCase {

	/**
	 * @return void
	 */
	public function testCreate() {
		$annotation = AnnotationFactory::create('@method', '\\Foo\\Model\\Entity\\Bar', 'doSth()', 1);
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@property', '\\Foo\\Model\\Entity\\Bar', '$baz', 1);
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@property', '\\Foo\\Model\\Entity\\Bar', 'baz', 1);
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@property-read', '\\Foo\\Model\\Entity\\Bar', 'baz', 1);
		$this->assertInstanceOf(PropertyReadAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@mixin', '\\Foo\\Model\\Entity\\Bar');
		$this->assertInstanceOf(MixinAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@uses', '\\Foo\\Model\\Entity\\Bar');
		$this->assertInstanceOf(UsesAnnotation::class, $annotation);

		$annotation = AnnotationFactory::create('@foooo', '\\Foo', '$foo');
		$this->assertNull($annotation);
	}

	/**
	 * @return void
	 */
	public function testCreateFromString() {
		/** @var \IdeHelper\Annotation\MethodAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@method \\Foo\\Model\\Entity\\Bar doSth($x, $y, $z)');
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);
		$this->assertSame('doSth($x, $y, $z)', $annotation->getMethod());

		/** @var \IdeHelper\Annotation\PropertyAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@property \\Foo\\Model\\Entity\\Bar $baz');
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);
		$this->assertSame('$baz', $annotation->getProperty());

		/** @var \IdeHelper\Annotation\PropertyAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@property \\Foo\\Model\\Entity\\Bar baz');
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);
		$this->assertSame('$baz', $annotation->getProperty());

		/** @var \IdeHelper\Annotation\PropertyAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@property \\Foo\\Model\\Entity\\Bar $baz Some comment :)');
		$this->assertInstanceOf(PropertyAnnotation::class, $annotation);
		$this->assertSame('Some comment :)', $annotation->getDescription());

		$annotation = AnnotationFactory::createFromString('@property\\Foo\\Model\\Entity\\Bar$baz');
		$this->assertNull($annotation);

		/** @var \IdeHelper\Annotation\PropertyReadAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@property-read \\Foo\\Model\\Entity\\Bar baz');
		$this->assertInstanceOf(PropertyReadAnnotation::class, $annotation);
		$this->assertSame('$baz', $annotation->getProperty());

		/** @var \IdeHelper\Annotation\MethodAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@method \\Foo\\Model\\Entity\\Bar complex($x, $y = [], $z = null)');
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);
		$this->assertSame('complex($x, $y = [], $z = null)', $annotation->getMethod());

		/** @var \IdeHelper\Annotation\MethodAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@method \\Foo\\Model\\Entity\\Bar complex($x, $y = [], $z = null) !');
		$this->assertInstanceOf(MethodAnnotation::class, $annotation);
		$this->assertSame('complex($x, $y = [], $z = null)', $annotation->getMethod());

		/** @var \IdeHelper\Annotation\MixinAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@mixin \\Foo\\Model\\Entity\\Bar');
		$this->assertInstanceOf(MixinAnnotation::class, $annotation);
		$this->assertSame('', $annotation->getDescription());

		/** @var \IdeHelper\Annotation\MixinAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@mixin \\Foo\\Model\\Entity\\Bar !');
		$this->assertInstanceOf(MixinAnnotation::class, $annotation);
		$this->assertSame('!', $annotation->getDescription());

		/** @var \IdeHelper\Annotation\UsesAnnotation $annotation */
		$annotation = AnnotationFactory::createFromString('@uses \\Foo\\Model\\Entity\\Bar');
		$this->assertInstanceOf(UsesAnnotation::class, $annotation);
		$this->assertSame('', $annotation->getDescription());
	}

}
