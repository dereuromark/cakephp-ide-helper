<?php

namespace IdeHelper\Annotation;

class MethodAnnotation extends AbstractAnnotation {

	/**
	 * @var string
	 */
	public const TAG = '@method';

	protected string $method;

	protected string $description;

	/**
	 * @param string $type
	 * @param string $method
	 * @param int|null $index
	 */
	public function __construct($type, $method, $index = null) {
		parent::__construct($type, $index);

		$description = '';
		$closingBrace = $this->findSignatureClosingParenthesis($method);
		if ($closingBrace !== null && isset($method[$closingBrace + 1]) && $method[$closingBrace + 1] === ' ') {
			$description = substr($method, $closingBrace + 2);
			$method = substr($method, 0, $closingBrace + 1);
		}

		$this->method = $method;
		$this->description = $description;
	}

	/**
	 * @param string $method
	 * @return int|null
	 */
	protected function findSignatureClosingParenthesis(string $method): ?int {
		$openingParenthesis = strpos($method, '(');
		if ($openingParenthesis === false) {
			return null;
		}

		$depth = 0;
		$quote = null;
		$length = strlen($method);
		for ($i = $openingParenthesis; $i < $length; $i++) {
			$char = $method[$i];
			if ($quote !== null) {
				if ($char === '\\') {
					$i++;

					continue;
				}
				if ($char === $quote) {
					$quote = null;
				}

				continue;
			}
			if ($char === '\'' || $char === '"') {
				$quote = $char;

				continue;
			}
			if ($char === '(') {
				$depth++;

				continue;
			}
			if ($char !== ')') {
				continue;
			}

			$depth--;
			if ($depth === 0) {
				return $i;
			}
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function getMethod(): string {
		return $this->method;
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

		return $this->type . ' ' . $this->method . $description;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation|\IdeHelper\Annotation\MethodAnnotation $annotation
	 *
	 * @return bool
	 */
	public function matches(AbstractAnnotation $annotation): bool {
		if (!$annotation instanceof self) {
			return false;
		}
		$methodName = substr($annotation->getMethod(), 0, strpos($annotation->getMethod(), '(') ?: 0);

		return $methodName === substr($this->method, 0, strpos($this->method, '(') ?: 0);
	}

	/**
	 * @param \IdeHelper\Annotation\MethodAnnotation $annotation
	 * @return void
	 */
	public function replaceWith(AbstractAnnotation $annotation): void {
		$this->type = $annotation->getType();
		$this->method = $annotation->getMethod();
	}

}
