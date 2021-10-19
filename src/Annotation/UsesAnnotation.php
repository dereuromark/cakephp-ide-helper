<?php

namespace IdeHelper\Annotation;

class UsesAnnotation extends AbstractAnnotation {

	/**
	 * @var string
	 */
	public const TAG = '@uses';

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param string $type
	 * @param int|null $index
	 */
	public function __construct($type, $index = null) {
		$description = '';
		if (strpos($type, ' ') !== false) {
			[$type, $description] = explode(' ', $type, 2);
		}

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
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\MixinAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool {
		if (!$annotation instanceof self) {
			return false;
		}
		if ($annotation->getType() !== $this->type) {
			return false;
		}

		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\MixinAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void {
		$this->type = $annotation->getType();
	}

}
