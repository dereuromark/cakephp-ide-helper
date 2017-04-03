<?php

namespace IdeHelper\Annotation;

use ArrayObject;

class AnnotationCollection extends ArrayObject {

	/**
	 * @param \ArrayObject $toMerge
	 *
	 * @return void
	 */
	public function merge(ArrayObject $toMerge) {
		$this->exchangeArray(array_merge($this->getArrayCopy(), (array)$toMerge));
	}

}
