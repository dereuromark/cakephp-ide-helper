<?php

namespace IdeHelper\Annotation;

class PropertyAnnotation extends AbstractAnnotation {

	const TAG = '@property';

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @param string $type
	 * @param string $property
	 * @param int|null $index
	 */
	public function __construct($type, $property, $index = null) {
		parent::__construct($type, $index);
		$this->property = $property;
	}

	/**
	 * @return string
	 */
	public function getProperty() {
		return $this->property;
	}

	/**
	 * @return string
	 */
	public function build() {
		return $this->type . ' ' . $this->property;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\PropertyAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation) {
		if ($annotation::TAG !== static::TAG) {
			return false;
		}
		if ($annotation->getProperty() !== $this->property) {
			return false;
		}

		return true;
	}

}
