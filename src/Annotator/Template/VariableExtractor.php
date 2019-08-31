<?php

namespace IdeHelper\Annotator\Template;

use PHP_CodeSniffer\Files\File;

class VariableExtractor {

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @return array
	 */
	public function extract(File $file): array {
		$tokens = $file->getTokens();

		$vars = [];

		//TODO

		return $vars;
	}
}
