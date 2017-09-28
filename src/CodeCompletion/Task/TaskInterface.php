<?php
namespace IdeHelper\CodeCompletion\Task;

interface TaskInterface {

	/**
	 * @return string
	 */
	public function type();

	/**
	 * @return array
	 */
	public function create();

}
