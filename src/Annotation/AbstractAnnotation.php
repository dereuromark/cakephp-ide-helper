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
	 * @return bool
	 */
	public function hasIndex() {
		return $this->index !== null;
	}

	/**
	 * @return int|null
	 */
	public function getIndex() {
		if ($this->index === null) {
			throw new RuntimeException('You cannot get an non-defined index. You can check with hasIndex() before calling this method.');
		}

		return $this->index;
	}

}
