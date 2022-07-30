<?php
namespace TestApp\Model\Entity;

class Foo {

	/**
	 * @link \TestApp\Model\Entity\Foo::$expected_release_type
	 *
	 * @return int|null
	 */
	protected function _getExpectedReleaseType(): ?int {
		$expected = $this->something;

		return $expected ?? null;
	}

	/**
	 * @link $something
	 *
	 * @return string|null
	 */
	protected function _getSomething(): ?string {
		$expected = $this->something;

		return $expected ?? null;
	}
}
