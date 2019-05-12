<?php

namespace IdeHelper\Test\TestCase\Annotation;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use RuntimeException;

class MixinAnnotationTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');

		$result = (string)$annotation;
		$this->assertSame('@mixin \\Foo\\Model\\Entity\\Bar', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new MixinAnnotation('\\Something\\Model\\Entity\\Else');

		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@mixin \\Something\\Model\\Entity\\Else', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new MixinAnnotation('\\Foo\\Model\\Entity\\BarBaz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new PropertyAnnotation('\\Foo\\Model\\Entity\\Bar', '$bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testMatchesWithDescription() {
		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar !');
		$comparisonAnnotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
		$this->assertSame('!', $annotation->getDescription());
		$this->assertSame('', $comparisonAnnotation->getDescription());

		$annotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new MixinAnnotation('\\Foo\\Model\\Entity\\Bar !');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
		$this->assertSame('', $annotation->getDescription());
		$this->assertSame('!', $comparisonAnnotation->getDescription());
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$annotation = new MixinAnnotation('', 1);

		$this->assertTrue($annotation->hasIndex());
		$this->assertSame(1, $annotation->getIndex());
	}

	/**
	 * @return void
	 */
	public function testIndexInvalidCall() {
		$annotation = new MixinAnnotation('');

		$this->assertFalse($annotation->hasIndex());

		$this->expectException(RuntimeException::class);

		$annotation->getIndex();
	}

}
