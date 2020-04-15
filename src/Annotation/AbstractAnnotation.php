<?php

namespace IdeHelper\Annotation;

use RuntimeException;

abstract class AbstractAnnotation implements AnnotationInterface, ReplacableAnnotationInterface {

	public const TAG = '';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var int|null
	 */
	protected $index;

	/**
	 * Needed for removing annotations
	 *
	 * @var bool
	 */
	protected $isInUse = false;

	/**
	 * @param string $type
	 * @param int|null $index
	 */
	public function __construct(string $type, ?int $index = null) {
		$this->type = $type;
		$this->index = $index;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return static::TAG . ' ' . $this->build();
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function hasIndex(): bool {
		return $this->index !== null;
	}

	/**
	 * @return int|null
	 * @throws \RuntimeException
	 */
	public function getIndex(): ?int {
		if ($this->index === null) {
			throw new RuntimeException('You cannot get an non-defined index. You can check with hasIndex() before calling this method.');
		}

		return $this->index;
	}

	/**
	 * @param bool $inUse
	 *
	 * @return void
	 */
	public function setInUse(bool $inUse = true): void {
		$this->isInUse = $inUse;
	}

	/**
	 * @return bool
	 */
	public function isInUse(): bool {
		return $this->isInUse;
	}

}
