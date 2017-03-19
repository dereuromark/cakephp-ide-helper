<?php

namespace IdeHelper\Annotation;

class MethodAnnotation extends AbstractAnnotation {

	const TAG = '@method';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @param string $type
	 * @param string $method
	 * @param int|null $index
	 */
	public function __construct($type, $method, $index = null) {
		parent::__construct($type, $index);
		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function build() {
		return $this->type . ' ' . $this->method;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\MethodAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation) {
		if ($annotation::TAG !== static::TAG) {
			return false;
		}
		$methodName = substr($annotation->getMethod(), 0, strpos($annotation->getMethod(), '('));
		if ($methodName !== substr($this->method, 0, strpos($this->method, '('))) {
			return false;
		}

		return true;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\MethodAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation) {
		$this->type = $annotation->getType();
		$this->method = $annotation->getMethod();
	}

}
