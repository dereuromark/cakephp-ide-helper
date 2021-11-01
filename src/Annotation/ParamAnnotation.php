<?php

namespace IdeHelper\Annotation;

class ParamAnnotation extends AbstractAnnotation {

	/**
	 * @var string
	 */
	public const TAG = '@param';

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
			[$variable, $description] = explode(' ', $variable, 2);
		}

		$this->variable = $variable;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getVariable(): string {
		return $this->variable;
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

		return $this->type . ' ' . $this->variable . $description;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\ParamAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool {
		if (!$annotation instanceof self) {
			return false;
		}
		if ($annotation->getVariable() !== $this->variable) {
			return false;
		}

		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\ParamAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void {
		$this->type = $annotation->getType();
		$this->variable = $annotation->getVariable();
	}

}
