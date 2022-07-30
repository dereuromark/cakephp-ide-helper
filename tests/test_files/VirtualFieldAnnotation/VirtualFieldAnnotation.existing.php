<?php
namespace TestApp\Foo;

class Foo {

	/**
	 * @link $expected_release_type
	 *
	 * @return int|null
	 */
	protected function _getExpectedReleaseType(): ?int
	{
		$expected = $this->something;

		return $expected ?? null;
	}
}
