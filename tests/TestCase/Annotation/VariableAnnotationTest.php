<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\VariableAnnotation;
use Tools\TestSuite\TestCase;

/**
 */
class VariableAnnotationTest extends TestCase {

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
		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');

		$result = (string)$annotation;
		$this->assertSame('@var \\Foo\\Model\\Table\\Bar $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new VariableAnnotation('\\Something\\Model\\Table\\Else', '$baz');

		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@var \\Something\\Model\\Table\\Else $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new VariableAnnotation('\\Something\\Else', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$bbb');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new MethodAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

}
