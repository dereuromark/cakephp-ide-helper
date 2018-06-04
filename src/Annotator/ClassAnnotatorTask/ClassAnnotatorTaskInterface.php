<?php
namespace IdeHelper\Annotator\ClassAnnotatorTask;

interface ClassAnnotatorTaskInterface {

	/**
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun($path, $content);

	/**
	 * @param string $path Path to file.
	 * @return void
	 */
	public function annotate($path);

}
