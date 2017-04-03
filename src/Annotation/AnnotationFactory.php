<?php

namespace IdeHelper\Annotation;

class AnnotationFactory {

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @param string $tag
	 * @param string $type
	 * @param string $content
	 * @param int|null $index
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	public static function create($tag, $type, $content, $index = null) {
		switch ($tag) {
			case PropertyAnnotation::TAG:
				return new PropertyAnnotation($type, $content, $index);
			case MethodAnnotation::TAG:
				return new MethodAnnotation($type, $content, $index);
			case VariableAnnotation::TAG:
				return new VariableAnnotation($type, $content, $index);
		}

		return null;
	}

	/**
	 * @param string $annotation (e.g. `@method \Foo\Bar myMethod($x)`)
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	public static function createFromString($annotation) {
		preg_match('/(.+?) (.+?) (.+)/', $annotation, $matches);
		if (!$matches) {
			return null;
		}
		return static::create($matches[1], $matches[2], $matches[3]);
	}

}
