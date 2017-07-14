<?php

namespace IdeHelper\Annotation;

class PropertyAnnotation extends AbstractAnnotation implements ReplacableAnnotationInterface {

	const TAG = '@property';

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param string $type
	 * @param string $property
	 * @param int|null $index
	 */
	public function __construct($type, $property, $index = null) {
		parent::__construct($type, $index);

		$description = '';
		if (strpos($property, ' ') !== false) {
			list($property, $description) = explode(' ', $property, 2);
		}
		if (substr($property, 0, 1) !== '$') {
			$property = '$' . $property;
		}

		$this->property = $property;
		$this->description = $description;
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
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function build() {
		$description = $this->description !== '' ? (' ' . $this->description) : '';

		return $this->type . ' ' . $this->property . $description;
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

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\PropertyAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation) {
		$this->type = $annotation->getType();
		$this->property = $annotation->getProperty();
	}

}
