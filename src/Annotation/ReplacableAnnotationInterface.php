<?php

namespace IdeHelper\Annotation;

interface ReplacableAnnotationInterface {

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool;

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 *
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void;

}
