<?php

namespace IdeHelper\Annotation;

class MethodAnnotation extends AbstractAnnotation {

	const TAG = '@method';

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param string $type
	 * @param string $method
	 * @param int|null $index
	 */
	public function __construct($type, $method, $index = null) {
		parent::__construct($type, $index);

		$description = '';
		$closingBrace = strrpos($method, ') ');
		if ($closingBrace !== false && $closingBrace !== strlen($method) - 1) {
			$description = substr($method, $closingBrace + 2);
			$method = substr($method, 0, $closingBrace + 1);
		}

		$this->method = $method;
		$this->description = $description;
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
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function build() {
		$description = $this->description !== '' ? (' ' . $this->description) : '';

		return $this->type . ' ' . $this->method . $description;
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
