<?php
namespace IdeHelper\Annotator\CallbackAnnotatorTask;

interface CallbackAnnotatorTaskInterface {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function shouldRun($path);

	/**
	 * @param string $path Path to file.
	 * @return void
	 */
	public function annotate($path);

}
