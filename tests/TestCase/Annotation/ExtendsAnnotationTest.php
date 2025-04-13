<?php

namespace IdeHelper\Test\TestCase\Annotation;

use IdeHelper\Annotation\ExtendsAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use RuntimeException;
use Shim\TestSuite\TestCase;

class ExtendsAnnotationTest extends TestCase {

	/**
	 * @return void
	 */
	public function testBuild() {
		$annotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');

		$result = (string)$annotation;
		$this->assertSame('@extends \Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>', $result);
	}

	/**
	 * @return void
	 */
	public function testReplaceWith() {
		$replacementAnnotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');

		$annotation = new ExtendsAnnotation('\Table<array{X: \Y>');
		$annotation->replaceWith($replacementAnnotation);

		$result = (string)$annotation;
		$this->assertSame('@extends \Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>', $result);
	}

	/**
	 * @return void
	 */
	public function testMatches() {
		$annotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');
		$comparisonAnnotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);

		$annotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');
		$comparisonAnnotation = new PropertyAnnotation('\\Foo\\Model\\Entity\\Bar', '$bar');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertFalse($result);

		$annotation = new ExtendsAnnotation('\Table<array{Sluggable: \Plugin\Model\Behavior\SluggableBehavior>');
		$comparisonAnnotation = new ExtendsAnnotation('\Table<array{Xyz: \Plugin\Model\Behavior\XyzBehavior>');
		$result = $annotation->matches($comparisonAnnotation);
		$this->assertTrue($result);
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$annotation = new ExtendsAnnotation('', 1);

		$this->assertTrue($annotation->hasIndex());
		$this->assertSame(1, $annotation->getIndex());
	}

	/**
	 * @return void
	 */
	public function testIndexInvalidCall() {
		$annotation = new ExtendsAnnotation('');

		$this->assertFalse($annotation->hasIndex());

		$this->expectException(RuntimeException::class);

		$annotation->getIndex();
	}

}
