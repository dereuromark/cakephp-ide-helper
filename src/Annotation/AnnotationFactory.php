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
		}

		return null;
	}

}
