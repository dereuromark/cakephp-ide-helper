<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use Tools\TestSuite\TestCase;

/**
 */
class MethodAnnotationTest extends TestCase {

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @return void
	 */
	public function testBuild() {
		$annotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');

		$result = (string)$annotation;
		$this->assertSame('@method \\Foo\\Model\\Entity\\Bar doSth()', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new MethodAnnotation('\\Something\\Model\\Entity\\Else', 'doSth(array $options = [])');

		$annotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@method \\Something\\Model\\Entity\\Else doSth(array $options = [])', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');
		$comparisonAnnotation = new MethodAnnotation('\\Something\\Else', 'doSth()');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');
		$comparisonAnnotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'sthElse()');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new MethodAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');
		$comparisonAnnotation = new PropertyAnnotation('\\Foo\\Model\\Entity\\Bar', 'doSth()');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$annotation = new MethodAnnotation('', '', 1);

		$this->assertTrue($annotation->hasIndex());
		$this->assertSame(1, $annotation->getIndex());
	}

	/**
	 * @expectedException \RuntimeException
	 * @return void
	 */
	public function testIndexInvalidCall() {
		$annotation = new MethodAnnotation('', '');

		$this->assertFalse($annotation->hasIndex());

		$annotation->getIndex();
	}

}
