<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use RuntimeException;

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

		$this->displayDiff($content, $newContent);
		$this->storeFile($path, $newContent);
		$this->content = $newContent;

		$this->report();

		return true;
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 * @param int $line
	 *
	 * @return bool
	 */
	protected function annotateInlineContent(string $path, string $content, array $annotations, int $line): bool {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->getFile($path, $content);

		$beginningOfLineIndex = $this->findFirstTokenOfLine($file, $line);
		if (!$beginningOfLineIndex) {
			return false;
		}

		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $beginningOfLineIndex - 1, null, true);
		if ($prevCode === false) {
			return false;
		}

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $beginningOfLineIndex - 1, $prevCode);
		$this->resetCounter();
		if ($closeTagIndex && $this->shouldSkip($file, $closeTagIndex)) {
			return false;
		}
		if ($closeTagIndex) {
			// Skip as there seems to be already one
			$newContent = $content;
		} else {
			$newContent = $this->addNewInlineDocBlock($file, $beginningOfLineIndex, $annotations);
		}

		if ($newContent === $content) {
			$this->reportSkipped();

			return false;
		}

		$this->displayDiff($content, $newContent);
		$this->storeFile($path, $newContent);
		$this->content = $newContent;

		$this->report();

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation>|array<string> $annotations
	 *
	 * @return string
	 */
	protected function addNewInlineDocBlock(File $file, int $index, array $annotations) {
		$tokens = $file->getTokens();

		foreach ($annotations as $key => $annotation) {
			if (is_string($annotation)) {
				continue;
			}
			$annotations[$key] = (string)$annotation;
		}

		if (count($annotations) !== 1) {
			throw new RuntimeException('Cannot work with annotation count != 1 right now');
		}

		$annotationString = '/** ' . $annotation . ' */';
		if (PHP_EOL !== "\n") {
			$annotationString = str_replace("\n", PHP_EOL, $annotationString);
		}
		$indentation = $this->detectIndentation($file, $index);

		$fixer = $this->getFixer($file);

		$docBlock = $indentation . $annotationString . PHP_EOL;
		$fixer->replaceToken($index, $docBlock . $tokens[$index]['content']);

		$contents = $fixer->getContents();

		$this->_counter[static::COUNT_ADDED] = count($annotations);

		return $contents;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $line
	 *
	 * @return int|null
	 */
	protected function findFirstTokenOfLine(File $file, int $line): ?int {
		$tokens = $file->getTokens();
		foreach ($tokens as $index => $token) {
			if ($token['line'] === $line) {
				return $index;
			}
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 *
	 * @return string
	 */
	protected function detectIndentation(File $file, int $index): string {
		$nextIndex = $file->findNext(T_WHITESPACE, $index + 1, null, true);
		if (!$nextIndex) {
			return '';
		}

		$tokens = $file->getTokens();
		$whitespace = '';
		for ($i = $index; $i < $nextIndex; $i++) {
			$whitespace .= $tokens[$i]['content'];
		}

		return $whitespace;
	}

}
