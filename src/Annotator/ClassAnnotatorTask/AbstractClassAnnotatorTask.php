<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use PHP_CodeSniffer\Util\Tokens;

abstract class AbstractClassAnnotatorTask extends AbstractAnnotator {

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 * @param string $content
	 */
	public function __construct(Io $io, array $config, $content) {
		parent::__construct($io, $config);

		$this->content = $content;
	}

	/**
	 * For testing only
	 *
	 * @return string
	 */
	public function getContent(): string {
		return $this->content;
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 *
	 * @return bool
	 */
	protected function annotateContent(string $path, string $content, array $annotations): bool {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->getFile($path, $content);

		$classOrTraitIndex = $file->findNext([T_CLASS, T_TRAIT], 0);
		if (!$classOrTraitIndex) {
			return false;
		}
		$beginningOfLineIndex = $this->beginningOfLine($file, $classOrTraitIndex);

		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $beginningOfLineIndex - 1, null, true);
		if ($prevCode === false) {
			return false;
		}

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $beginningOfLineIndex - 1, $prevCode);
		$this->resetCounter();
		if ($closeTagIndex && $this->shouldSkip($file, $closeTagIndex)) {
			return false;
		}
		if ($closeTagIndex && !$this->isInlineDocBlock($file, $closeTagIndex)) {
			$newContent = $this->appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$newContent = $this->addNewDocBlock($file, $beginningOfLineIndex, $annotations);
		}

		if ($newContent === $content) {
			$this->reportSkipped();

			return false;
		}

		$this->content = $newContent;

		$this->displayDiff($content, $newContent);
		$this->storeFile($path, $newContent);

		$this->report();

		return true;
	}

}
