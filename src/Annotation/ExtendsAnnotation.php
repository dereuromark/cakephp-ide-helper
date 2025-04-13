<?php

namespace IdeHelper\Annotation;

class ExtendsAnnotation extends AbstractAnnotation {

	/**
	 * @var string
	 */
	public const TAG = '@extends';

	protected string $description;

	/**
	 * @param string $type
	 * @param int|null $index
	 */
	public function __construct(string $type, ?int $index = null) {
		$description = '';

		parent::__construct($type, $index);

		$this->description = $description;
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

		return $this->type . $description;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\ExtendsAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool {
		if (!$annotation instanceof self) {
			return false;
		}

		// Always matches as there can only be one per docblock
		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\ExtendsAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void {
		$this->type = $annotation->getType();
	}

}
