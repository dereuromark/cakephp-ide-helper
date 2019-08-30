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
	 * @param array $config
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
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $annotations
	 *
	 * @return bool
	 */
	protected function _annotate($path, $content, array $annotations) {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->_getFile($path, $content);

		$classOrTraitIndex = $file->findNext([T_CLASS, T_TRAIT], 0);
		if (!$classOrTraitIndex) {
			return false;
		}
		$beginningOfLineIndex = $this->_beginningOfLine($file, $classOrTraitIndex);

		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $beginningOfLineIndex - 1, null, true);

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $beginningOfLineIndex - 1, $prevCode);
		$this->_resetCounter();
		if ($closeTagIndex && $this->shouldSkip($file, $closeTagIndex)) {
			return false;
		}
		if ($closeTagIndex && !$this->isInlineDocBlock($file, $closeTagIndex)) {
			$newContent = $this->_appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$newContent = $this->_addNewDocBlock($file, $beginningOfLineIndex, $annotations);
		}

		if ($newContent === $content) {
			$this->_reportSkipped();
			return false;
		}

		$this->content = $newContent;

		$this->_displayDiff($content, $newContent);
		$this->_storeFile($path, $newContent);

		$this->_report();

		return true;
	}

}
