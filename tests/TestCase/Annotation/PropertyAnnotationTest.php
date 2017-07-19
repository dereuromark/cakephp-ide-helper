<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use Tools\TestSuite\TestCase;

/**
 */
class PropertyAnnotationTest extends TestCase {

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
		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');

		$result = (string)$annotation;
		$this->assertSame('@property \\Foo\\Model\\Table\\Bar $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new PropertyAnnotation('\\Something\\Model\\Table\\Else', '$baz');

		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@property \\Something\\Model\\Table\\Else $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new PropertyAnnotation('\\Something\\Else', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$bbb');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new MethodAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testMatchesWithoutVariableChar() {
		$annotation = new PropertyAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new PropertyAnnotation('\\Something\\Else', 'baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
	}

}
