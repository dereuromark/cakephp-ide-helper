<?php

namespace IdeHelper\Test\TestCase\Annotation;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\ParamAnnotation;

class ParamAnnotationTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');

		$result = (string)$annotation;
		$this->assertSame('@param \\Foo\\Model\\Table\\Bar $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new ParamAnnotation('\\Something\\Model\\Table\\Else', '$baz');

		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@param \\Something\\Model\\Table\\Else $baz', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new ParamAnnotation('\\Something\\Else', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$bbb');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$comparisonAnnotation = new MethodAnnotation('\\Foo\\Model\\Table\\Bar', '$baz');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testMatchesWithDescription() {
		$annotation = new ParamAnnotation('\\Foo\\Model\\Table\\Bar', '$baz !');
		$comparisonAnnotation = new ParamAnnotation('\\Something\\Else', '$baz');
		$result = $annotation->matches($comparisonAnnotation);

		$this->assertTrue($result);
		$this->assertSame('!', $annotation->getDescription());
		$this->assertSame('', $comparisonAnnotation->getDescription());
	}

}
