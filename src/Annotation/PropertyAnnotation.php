<?php

namespace IdeHelper\Annotation;

class PropertyAnnotation extends AbstractAnnotation {

	/**
	 * @var string
	 */
	public const TAG = '@property';

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
			[$property, $description] = explode(' ', $property, 2);
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
	public function getProperty(): string {
		return $this->property;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function build(): string {
		$description = $this->description !== '' ? (' ' . $this->description) : '';

		return $this->type . ' ' . $this->property . $description;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\PropertyAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool {
		if (!$annotation instanceof self) {
			return false;
		}
		if ($annotation->getProperty() !== $this->property) {
			return false;
		}

		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\PropertyAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void {
		$this->type = $annotation->getType();
		$this->property = $annotation->getProperty();
	}

}
