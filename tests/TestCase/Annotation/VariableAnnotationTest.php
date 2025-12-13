<?php

namespace IdeHelper\Test\TestCase\Annotation;

use Cake\TestSuite\TestCase;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\VariableAnnotation;

class VariableAnnotationTest extends TestCase {

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

	/**
	 * @return void
	 */
	public function testMatchesWithDescription() {
		$annotation = new VariableAnnotation('\\Foo\\Model\\Table\\Bar', '$baz !');
		$comparisonAnnotation = new VariableAnnotation('\\Something\\Else', '$baz');
		$result = $annotation->matches($comparisonAnnotation);

		$this->assertTrue($result);
		$this->assertSame('!', $annotation->getDescription());
		$this->assertSame('', $comparisonAnnotation->getDescription());
	}

	/**
	 * @return void
	 */
	public function testReplaceWithPreservesNullableForGuessed() {
		// Existing annotation has |null
		$annotation = new VariableAnnotation('\\App\\Model\\Entity\\Home|null', '$homeData');

		// New guessed annotation without null
		$replacementAnnotation = new VariableAnnotation('mixed', '$homeData');
		$replacementAnnotation->setGuessed(true);

		$annotation->replaceWith($replacementAnnotation);

		// Should preserve |null
		$result = (string)$annotation;
		$this->assertSame('@var mixed|null $homeData', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWithDoesNotAddNullForNonGuessed() {
		// Existing annotation has |null
		$annotation = new VariableAnnotation('\\App\\Model\\Entity\\Home|null', '$homeData');

		// New non-guessed annotation without null
		$replacementAnnotation = new VariableAnnotation('\\App\\Model\\Entity\\User', '$homeData');

		$annotation->replaceWith($replacementAnnotation);

		// Should NOT preserve |null for non-guessed
		$result = (string)$annotation;
		$this->assertSame('@var \\App\\Model\\Entity\\User $homeData', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWithDoesNotDuplicateNull() {
		// Existing annotation has |null
		$annotation = new VariableAnnotation('\\App\\Model\\Entity\\Home|null', '$homeData');

		// New guessed annotation already has null
		$replacementAnnotation = new VariableAnnotation('object|null', '$homeData');
		$replacementAnnotation->setGuessed(true);

		$annotation->replaceWith($replacementAnnotation);

		// Should NOT duplicate |null
		$result = (string)$annotation;
		$this->assertSame('@var object|null $homeData', $result);
	}

}
