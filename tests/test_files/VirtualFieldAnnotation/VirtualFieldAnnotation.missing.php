<?php
namespace TestApp\Model\Entity;

class Foo {

	/**
	 * @return int|null
	 */
	protected function _getExpectedReleaseType(): ?int
	{
		$expected = $this->something;

		return $expected ?? null;
	}
}
