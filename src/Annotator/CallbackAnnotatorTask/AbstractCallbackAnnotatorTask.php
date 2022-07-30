<?php

namespace IdeHelper\Annotator\CallbackAnnotatorTask;

use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

abstract class AbstractCallbackAnnotatorTask extends AbstractAnnotator {

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 * @param string $path
	 * @param string $content
	 */
	public function __construct(Io $io, array $config, $path, $content) {
		parent::__construct($io, $config);

		$this->path = $path;
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
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return array<array<string, mixed>>
	 */
	protected function getMethods(File $file) {
		$methods = [];
		$currentIndex = 0;
		while (true) {
			$methodIndex = $file->findNext(T_FUNCTION, $currentIndex);
			if (!$methodIndex) {
				break;
			}
			$methods[$methodIndex] = $this->parseMethod($file, $methodIndex);

			$currentIndex = $methodIndex + 1;
		}

		return $methods;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @return array<string, mixed>
	 */
	protected function parseMethod(File $file, $index) {
		$tokens = $file->getTokens();
		$nameIndex = $file->findNext(Tokens::$emptyTokens, $index + 1, null, true);

		$closeTagIndex = $this->findCloseTagIndex($file, $index);

		$result = [
			'name' => $tokens[$nameIndex]['content'],
			'docBlockStart' => $closeTagIndex ? $tokens[$closeTagIndex]['comment_opener'] : null,
			'docBlockEnd' => $closeTagIndex ?: null,
		];

		return $result;
	}

	/**
	 * @param string $path
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<array<string, mixed>> $methods
	 * @return bool
	 */
	protected function annotateMethods(string $path, File $file, array $methods): bool {
		$this->resetCounter();

		$fixer = $this->getFixer($file);

		$fixer->beginChangeset();

		foreach ($methods as $method) {
			/** @var array<\IdeHelper\Annotation\ParamAnnotation> $replacingAnnotations */
			$replacingAnnotations = $method['annotations'];
			foreach ($replacingAnnotations as $annotation) {
				$fixer->replaceToken($annotation->getIndex(), $annotation->build());
				$this->_counter[static::COUNT_UPDATED]++;
			}
		}

		$fixer->endChangeset();

		$newContent = $fixer->getContents();

		if ($newContent === $this->content) {
			$this->reportSkipped();

			return false;
		}

		$this->displayDiff($this->content, $newContent);
		$this->storeFile($path, $newContent);
		$this->content = $newContent;

		$this->report();

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @return int|null
	 */
	protected function findCloseTagIndex(File $file, int $index): ?int {
		$tokens = $file->getTokens();

		$beginningOfLineIndex = $index;
		while ($tokens[$beginningOfLineIndex - 1]['line'] === $tokens[$index]['line']) {
			$beginningOfLineIndex--;
		}

		$prevCodeIndex = $file->findPrevious(Tokens::$emptyTokens, $beginningOfLineIndex - 1, null, true);
		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $index - 1, $prevCodeIndex ?: null);

		return $closeTagIndex ?: null;
	}

}
