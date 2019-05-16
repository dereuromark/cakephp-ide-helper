<?php
namespace IdeHelper\CodeCompletion\Task;

interface TaskInterface {

	/**
	 * @return string
	 */
	public function type();

	/**
	 * @return string
	 */
	public function create();

}
