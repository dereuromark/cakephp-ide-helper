<?php

namespace IdeHelper\Annotation;

interface AnnotationInterface {

	/**
	 * @return string
	 */
	public function getDescription();

	/**
	 * @return string
	 */
	public function build();

}
