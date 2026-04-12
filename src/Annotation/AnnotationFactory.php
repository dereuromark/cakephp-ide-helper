<?php

namespace IdeHelper\Annotation;

use RuntimeException;

class AnnotationFactory {

	protected string $property;

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
				if ($content) {
					$type .= ' ' . $content;
				}

				return new MixinAnnotation($type, $index);
			case ParamAnnotation::TAG:
				return new ParamAnnotation($type, (string)$content, $index);
			case UsesAnnotation::TAG:
				if ($content) {
					$type .= ' ' . $content;
				}

				return new UsesAnnotation($type, $index);
			case ExtendsAnnotation::TAG:
				if ($content) {
					$type .= ' ' . $content;
				}

				return new ExtendsAnnotation($type, $index);
			case LinkAnnotation::TAG:
				if ($content) {
					$type .= ' ' . $content;
				}

				return new LinkAnnotation($type, $index);
			case SeeAnnotation::TAG:
				if ($content) {
					$type .= ' ' . $content;
				}

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
		preg_match('/^@extends ([^\s!]+<.*?>)(?:\s*([!]))?/', $annotation, $matches);
		if ($matches) {
			$string = implode(' ', $matches);

			return static::create(ExtendsAnnotation::TAG, $string);
		}

		// Split `@tag <type> <rest>`: the type may contain spaces inside generic
		// brackets (e.g. `Foo<int, Bar>`), so naive `[^ ]+` would cut it short.
		// Walk the type char-by-char, tracking `<>` depth, and stop at the first
		// top-level space.
		if (preg_match('/^(@property|@property-read|@method|@var|@param) (.+)$/', $annotation, $matches)) {
			$tag = $matches[1];
			$rest = $matches[2];
			$depth = 0;
			$len = strlen($rest);
			$split = -1;
			for ($i = 0; $i < $len; $i++) {
				$c = $rest[$i];
				if ($c === '<') {
					$depth++;
				} elseif ($c === '>') {
					$depth = max(0, $depth - 1);
				} elseif ($c === ' ' && $depth === 0) {
					$split = $i;

					break;
				}
			}
			if ($split <= 0) {
				return null;
			}
			$type = substr($rest, 0, $split);
			$content = substr($rest, $split + 1);

			return static::create($tag, $type, $content);
		}

		return null;
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
