<?php

namespace IdeHelper\Annotation;

class VariableAnnotation extends AbstractAnnotation implements ReplacableAnnotationInterface {

	const TAG = '@var';

	/**
	 * @var string
	 */
	protected $variable;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param string $type
	 * @param string $variable
	 * @param int|null $index
	 */
	public function __construct($type, $variable, $index = null) {
		parent::__construct($type, $index);

		$description = '';
		if (strpos($variable, ' ') !== false) {
			list($variable, $description) = explode(' ', $variable, 2);
		}
		$this->variable = $variable;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getVariable() {
		return $this->variable;
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

		return $this->type . ' ' . $this->variable . $description;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\VariableAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation) {
		if ($annotation::TAG !== static::TAG) {
			return false;
		}
		if ($annotation->getVariable() !== $this->variable) {
			return false;
		}

		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\VariableAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation) {
		$this->type = $annotation->getType();
		$this->variable = $annotation->getVariable();
	}

}
