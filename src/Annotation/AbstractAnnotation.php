<?php

namespace IdeHelper\Annotation;

use RuntimeException;

abstract class AbstractAnnotation {

	const TAG = '';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string|null
	 */
	protected $classNameToReplace;

	/**
	 * @var int|null
	 */
	protected $index;

	/**
	 * @param string $type
	 * @param int|null $index
	 */
	public function __construct($type, $index = null) {
		$this->type = $type;
		$this->index = $index;
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function replaceClassName($type) {
		$this->classNameToReplace = $type;
	}

	/**
	 * @return string
	 */
	abstract public function build();

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 *
	 * @return bool
	 */
	abstract public function matches(self $annotation);

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 *
	 * @return void
	 */
	abstract public function replaceWith(self $annotation);

	/**
	 * @return string
	 */
	public function __toString() {
		return static::TAG . ' ' . $this->build();
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	/**
	 * @return int|null
	 */
	public function getIndex() {
		if ($this->index === null) {
			throw new RuntimeException('You cannot get an non-defined index.');
		}

		return $this->index;
	}

}
