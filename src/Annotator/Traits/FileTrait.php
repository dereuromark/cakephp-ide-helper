<?php

namespace IdeHelper\Annotator\Traits;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;

trait FileTrait {

	/**
	 * @param string $file
	 * @param string|null $content
	 *
	 * @return \PHP_CodeSniffer\Files\File
	 */
	protected function getFile(string $file, ?string $content = null): File {
		$_SERVER['argv'] = [];

		$phpcs = new Runner();

		if (!defined('PHP_CODESNIFFER_CBF')) {
			define('PHP_CODESNIFFER_CBF', false);
		}
		// Explictly specifying standard prevents it from searching for phpcs.xml type files.
		$config = new Config(['--standard=PSR2']);
		$phpcs->config = $config;
		$phpcs->init();

		$ruleset = new Ruleset($config);

		$fileObject = new File($file, $ruleset, $config);
		$fileObject->setContent($content ?? (string)file_get_contents($file));
		$fileObject->parse();

		return $fileObject;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return \PHP_CodeSniffer\Fixer
	 */
	protected function getFixer(File $file): Fixer {
		$fixer = new Fixer();

		$fixer->startFile($file);

		return $fixer;
	}

}
