<?php

namespace IdeHelper\Illuminator\Task;

use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Reads the annotated properties of an entity and adds the constants based on those.
 */
class EntityFieldTask extends AbstractTask {

	use FileTrait;

	const PREFIX = 'FIELD_';

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'visibility' => null,
	];

	/**
	 * @var bool|null
	 */
	protected $_visibility;

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun($path) {
		return (bool)preg_match('#\\/Model\\/Entity/.+\\.php$#', $path);
	}

	/**
	 * @param string $content
	 * @param string $path Path to file.
	 * @return string
	 */
	public function run($content, $path) {
		$file = $this->_getFile('', $content);

		$classIndex = $file->findNext(T_CLASS, 0);
		if (!$classIndex) {
			return $content;
		}

		$tokens = $file->getTokens();

		$fields = $this->getFields($file, $classIndex);

		$existingConstants = $this->getFieldConstants($tokens[$classIndex]['scope_opener'], $tokens[$classIndex]['scope_closer']);
		if ($existingConstants) {
			$index = null; //TODO
		} else {
			$index = $file->findNext(T_WHITESPACE, $tokens[$classIndex]['scope_opener'] + 1, $tokens[$classIndex]['scope_closer'], true);
			if ($index === false) {
				$index = $tokens[$classIndex]['scope_closer'];
			}
		}

		return $this->addClassConstants($file, $fields, $index, 0) ?: $content;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $classIndex
	 * @return array
	 */
	protected function getFields(File $file, $classIndex) {
		$tokens = $file->getTokens();

		$docBlockCloseTagIndex = $this->_findDocBlockCloseTagIndex($file, $classIndex);
		if (!$docBlockCloseTagIndex || empty($tokens[$docBlockCloseTagIndex]['comment_opener'])) {
			return [];
		}

		$fields = [];
		for ($i = $tokens[$docBlockCloseTagIndex]['comment_opener'] + 1; $i < $docBlockCloseTagIndex; $i++) {

			if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
				continue;
			}
			if ($tokens[$i]['content'] !== '@property') {
				continue;
			}

			$pieces = explode(' ', $tokens[$i + 2]['content']);
			if (count($pieces) < 2) {
				continue;
			}
			$field = mb_substr($pieces[1], 1);
			$fields[$field] = [
				'name' => $field,
				//constant
				//index
			];
		}

		return $fields;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index First functional code after docblock
	 *
	 * @return int|false
	 */
	protected function _findDocBlockCloseTagIndex(File $file, $index) {
		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $index - 1, null, true);
		if (!$prevCode) {
			return false;
		}

		return $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $index - 1, $prevCode);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array $fields
	 * @param int $index
	 * @param int $level
	 * @return string|null
	 */
	protected function addClassConstants(File $file, array $fields, $index, $level = 1) {
		if (!$fields) {
			return null;
		}

		$tokens = $file->getTokens();

		$whitespace = '';
		$firstOfLineIndex = $index;
		while ($tokens[$firstOfLineIndex - 1]['line'] === $tokens[$index]['line']) {
			$firstOfLineIndex--;
			$whitespace .= $tokens[$firstOfLineIndex]['content'];
		}
		if ($level < 1) {
			$whitespace = str_repeat(' ', 4);
		}

		$beginIndex = $firstOfLineIndex - 1;
		$visibility = '';
		if ($this->visibility()) {
			$visibility = 'public ';
		}

		$fixer = $this->_getFixer($file);

		$fixer->beginChangeset();

		foreach ($fields as $field) {
			$constant = 'FIELD_' . mb_strtoupper($field['name']);

			$fixer->addContent($beginIndex, $whitespace . $visibility . 'const ' . $constant . ' = \'' . $field['name'] . '\';');
			$fixer->addNewline($beginIndex);
		}

		$fixer->addNewline($beginIndex);

		$fixer->endChangeset();

		return $fixer->getContents();
	}

	/**
	 * @param int $startIndex
	 * @param int $endIndex
	 * @return array
	 */
	protected function getFieldConstants($startIndex, $endIndex) {
		//TODO

		return [];
	}

	/**
	 * @return bool
	 */
	protected function visibility() {
		if ($this->_visibility !== null) {
			return $this->_visibility;
		}

		$visConfig = $this->getConfig('visibility');
		if ($visConfig === null) {
			$visConfig = version_compare(PHP_VERSION, '7.1') >= 0;
		}

		return $this->_visibility = $visConfig;
	}

}
