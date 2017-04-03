<?php

namespace IdeHelper\Annotation;

class VariableAnnotation extends AbstractAnnotation {

	const TAG = '@var';

	/**
	 * @var string
	 */
	protected $variable;

	/**
	 * @param string $type
	 * @param string $variable
	 * @param int|null $index
	 */
	public function __construct($type, $variable, $index = null) {
		parent::__construct($type, $index);
		$this->variable = $variable;
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
	public function build() {
		return $this->type . ' ' . $this->variable;
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
