<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotation\UsesAnnotation;
use RuntimeException;
use Shim\TestSuite\TestCase;

class UsesAnnotationTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');

		$result = (string)$annotation;
		$this->assertSame('@uses \\Foo\\Model\\Entity\\Bar', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new UsesAnnotation('\\Something\\Model\\Entity\\Else');

		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@uses \\Something\\Model\\Entity\\Else', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new UsesAnnotation('\\Foo\\Model\\Entity\\BarBaz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new PropertyAnnotation('\\Foo\\Model\\Entity\\Bar', '$bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testMatchesWithDescription() {
		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar !');
		$comparisonAnnotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
		$this->assertSame('!', $annotation->getDescription());
		$this->assertSame('', $comparisonAnnotation->getDescription());

		$annotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar');
		$comparisonAnnotation = new UsesAnnotation('\\Foo\\Model\\Entity\\Bar !');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
		$this->assertSame('', $annotation->getDescription());
		$this->assertSame('!', $comparisonAnnotation->getDescription());
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$annotation = new UsesAnnotation('', 1);

		$this->assertTrue($annotation->hasIndex());
		$this->assertSame(1, $annotation->getIndex());
	}

	/**
	 * @return void
	 */
	public function testIndexInvalidCall() {
		$annotation = new UsesAnnotation('');

		$this->assertFalse($annotation->hasIndex());

		$this->expectException(RuntimeException::class);

		$annotation->getIndex();
	}

}
