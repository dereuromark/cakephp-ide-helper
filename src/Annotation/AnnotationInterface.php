<?php

namespace IdeHelper\Annotation;

interface AnnotationInterface {

	/**
	 * @return string
	 */
	public function getDescription(): string;

	/**
	 * @return string
	 */
	public function build(): string;

}
