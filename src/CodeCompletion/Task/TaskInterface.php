<?php

namespace IdeHelper\CodeCompletion\Task;

interface TaskInterface {

	/**
	 * @return string
	 */
	public function type(): string;

	/**
	 * @return string
	 */
	public function create(): string;

}
