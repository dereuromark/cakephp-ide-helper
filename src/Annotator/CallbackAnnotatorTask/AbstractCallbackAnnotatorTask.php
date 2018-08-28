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
	 * @param array $config
	 * @param string $path
	 * @param string $content
	 */
	public function __construct(Io $io, array $config, $path, $content) {
		parent::__construct($io, $config);

		$this->path = $path;
		$this->content = $content;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return array
	 */
	protected function _getMethods(File $file) {
		$methods = [];
		$currentIndex = 0;
		while (true) {
			$methodIndex = $file->findNext(T_FUNCTION, $currentIndex);
			if (!$methodIndex) {
				break;
			}
			$methods[$methodIndex] = $this->_parseMethod($file, $methodIndex);

			$currentIndex = $methodIndex + 1;
		}

		return $methods;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @return array
	 */
	protected function _parseMethod(File $file, $index) {
		$tokens = $file->getTokens();
		$nameIndex = $file->findNext(Tokens::$emptyTokens, $index + 1, null, true);

		$closeTagIndex = $this->_findCloseTagIndex($file, $index);

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
	 * @param array $methods
	 * @return bool
	 */
	protected function _annotateMethods($path, File $file, array $methods) {
		$this->_resetCounter();

		$fixer = $this->_getFixer($file);

		$fixer->beginChangeset();

		foreach ($methods as $method) {
			/** @var \IdeHelper\Annotation\ParamAnnotation[] $replacingAnnotations */
			$replacingAnnotations = $method['annotations'];
			foreach ($replacingAnnotations as $annotation) {
				$fixer->replaceToken($annotation->getIndex(), $annotation->build());
				$this->_counter[static::COUNT_UPDATED]++;
			}
		}

		$fixer->endChangeset();

		$newContent = $fixer->getContents();

		if ($newContent === $this->content) {
			$this->_reportSkipped();
			return false;
		}

		$this->_displayDiff($this->content, $newContent);
		$this->_storeFile($path, $newContent);

		$this->_report();

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index
	 * @return int|null
	 */
	protected function _findCloseTagIndex(File $file, $index) {
		$tokens = $file->getTokens();

		$firstLineIndex = $index;
		while ($tokens[$firstLineIndex - 1]['line'] === $tokens[$index]['line']) {
			$firstLineIndex--;
		}

		$prevCodeIndex = $file->findPrevious(Tokens::$emptyTokens, $firstLineIndex - 1, null, true);
		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $index - 1, $prevCodeIndex ?: null);

		return $closeTagIndex ?: null;
	}

}
