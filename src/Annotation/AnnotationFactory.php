<?php

namespace IdeHelper\Annotation;

use RuntimeException;

class AnnotationFactory {

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @param string $tag
	 * @param string $type
	 * @param string|null $content
	 * @param int|null $index
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	public static function create($tag, $type, $content = null, $index = null) {
		switch ($tag) {
			case PropertyAnnotation::TAG:
				return new PropertyAnnotation($type, (string)$content, $index);
			case PropertyReadAnnotation::TAG:
				return new PropertyReadAnnotation($type, (string)$content, $index);
			case MethodAnnotation::TAG:
				return new MethodAnnotation($type, (string)$content, $index);
			case VariableAnnotation::TAG:
				return new VariableAnnotation($type, (string)$content, $index);
			case MixinAnnotation::TAG:
				return new MixinAnnotation($type, $index);
			case ParamAnnotation::TAG:
				return new ParamAnnotation($type, (string)$content, $index);
			case UsesAnnotation::TAG:
				return new UsesAnnotation($type, $index);
			case LinkAnnotation::TAG:
				return new LinkAnnotation($type, $index);
			case SeeAnnotation::TAG:
				return new SeeAnnotation($type, $index);
		}

		return null;
	}

	/**
	 * @param string $annotation (e.g. `@method \Foo\Bar myMethod($x, $y)`)
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	public static function createFromString($annotation) {
		preg_match('/^@mixin (.+)\s*(.+)?$/', $annotation, $matches);
		if ($matches) {
			return static::create(MixinAnnotation::TAG, $matches[1]);
		}
		preg_match('/^@uses (.+)\s*(.+)?$/', $annotation, $matches);
		if ($matches) {
			return static::create(UsesAnnotation::TAG, $matches[1]);
		}

		preg_match('/^(@property|@property-read|@method|@var|@param) ([^ ]+) (.+)$/', $annotation, $matches);
		if (!$matches) {
			return null;
		}

		return static::create($matches[1], $matches[2], $matches[3]);
	}

	/**
	 * @param string $tag
	 * @param string $type
	 * @param string|null $content
	 * @param int|null $index
	 * @throws \RuntimeException
	 * @return \IdeHelper\Annotation\AbstractAnnotation
	 */
	public static function createOrFail($tag, $type, $content = null, $index = null) {
		$annotation = static::create($tag, $type, $content, $index);
		if (!$annotation) {
			throw new RuntimeException('Cannot create annotation for tag ' . $tag . ', type ' . $type);
		}

		return $annotation;
	}

}
