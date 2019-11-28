<?php

namespace IdeHelper\Annotator\CallbackAnnotatorTask;

interface CallbackAnnotatorTaskInterface {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function shouldRun(string $path): bool;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool;

}
