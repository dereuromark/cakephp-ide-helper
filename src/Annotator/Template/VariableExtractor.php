<?php

namespace IdeHelper\Annotator\Template;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Extracts variables from CakePHP php/ctp templates using token list of CS File object.
 */
class VariableExtractor {

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @return array<string, mixed>
	 */
	public function extract(File $file) {
		$vars = $this->collect($file);

		$result = [];
		foreach ($vars as $var) {
			/** @var string $name */
			$name = $var['name'];
			if (!isset($result[$name])) {
				$result[$name] = $var;
				$result[$name]['vars'][] = $var;
			}

			if ($var['excludeReason']) {
				$result[$name]['excludeReason'] = $var['excludeReason'];
			}
			if ($var['type'] && empty($result[$name]['type'])) {
				$result[$name]['type'] = $var['type'];
			}
			if ($var['type'] && !empty($result[$name]['type']) && $var['type'] !== $result[$name]['type']) {
				$result[$name]['type'] = 'mixed';
			}
		}

		ksort($result);

		return $result;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return array<array<string, mixed>>
	 */
	protected function collect(File $file) {
		$tokens = $file->getTokens();

		$vars = [];
		foreach ($tokens as $i => $token) {
			if ($token['code'] !== T_VARIABLE) {
				continue;
			}
			if ($token['content'] === '$this') {
				continue;
			}

			$var = $this->getVar($file, $token, $i);
			if (!$var) {
				continue;
			}

			$vars[$i] = $var;
		}

		return $vars;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $token
	 * @param int $index
	 * @return array<string, mixed>
	 */
	protected function getVar(File $file, array $token, $index) {
		$variable = substr($token['content'], 1);

		$result = [
			'name' => $variable,
			'index' => $index,
			'type' => null,
			'excludeReason' => null,
			'context' => $token,
		];
		$type = $this->getVarType($file, $result);
		$result['type'] = $type;
		$excludeReason = $this->getExcludeReason($file, $result);
		$result['excludeReason'] = $excludeReason;

		return $result;
	}

	/**
	 * Guesses the variable type based on PHP token elements before or after.
	 *
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @return string|null
	 */
	protected function getVarType(File $file, array $result) {
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(Tokens::$emptyTokens, $result['index'] + 1, $result['index'] + 3, true, null, true);
		if ($nextIndex && $tokens[$nextIndex]['code'] === T_OBJECT_OPERATOR) {
			return 'object';
		}

		if ($nextIndex && $tokens[$nextIndex]['code'] === T_OPEN_SQUARE_BRACKET) {
			return 'array';
		}

		$prevIndex = $file->findPrevious(Tokens::$emptyTokens, $result['index'] - 1, $result['index'] - 3, true, null, true);
		if ($prevIndex && in_array($tokens[$prevIndex]['code'], [T_ECHO, T_OPEN_TAG_WITH_ECHO, T_STRING_CONCAT], true)) {
			if ($nextIndex && in_array($tokens[$nextIndex]['code'], [T_SEMICOLON, T_STRING_CONCAT, T_CLOSE_TAG], true)) {
				return 'string';
			}
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @return string|null
	 */
	protected function getExcludeReason(File $file, array $result) {
		if ($this->isLoopVar($file, $result)) {
			return 'Declared in loop';
		}
		if ($this->isTryCatchVar($file, $result)) {
			return 'Try catch';
		}

		if ($this->isAssignment($file, $result)) {
			return 'Assignment';
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @return bool
	 */
	protected function isLoopVar(File $file, array $result) {
		$tokens = $file->getTokens();

		$prevIndex = $file->findPrevious(Tokens::$emptyTokens, $result['index'] - 1, $result['index'] - 3, true, null, true);
		if ($prevIndex && $tokens[$prevIndex]['code'] === T_AS) {
			return true;
		}

		if ($prevIndex && $tokens[$prevIndex]['code'] === T_DOUBLE_ARROW && $this->isInLoop($file, $result, $prevIndex)) {
			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @param int $assignmentIndex
	 *
	 * @return bool
	 */
	protected function isInLoop(File $file, array $result, $assignmentIndex) {
		if (empty($result['context']['nested_parenthesis'])) {
			return false;
		}

		$startIndex = null;
		foreach ($result['context']['nested_parenthesis'] as $key => $unused) {
			$startIndex = $key;

			break;
		}

		return (bool)$file->findPrevious(T_FOREACH, $startIndex - 1, $startIndex - 3);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @return bool
	 */
	protected function isTryCatchVar(File $file, array $result) {
		if (empty($result['context']['nested_parenthesis'])) {
			return false;
		}

		$startIndex = null;
		foreach ($result['context']['nested_parenthesis'] as $key => $unused) {
			$startIndex = $key;

			break;
		}

		if ($startIndex === null || $startIndex <= 1) {
			return false;
		}

		return (bool)$file->findPrevious(T_CATCH, $startIndex - 1, $startIndex - 3);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<string, mixed> $result
	 * @return bool
	 */
	protected function isAssignment(File $file, array $result) {
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(Tokens::$emptyTokens, $result['index'] + 1, $result['index'] + 3, true, null, true);
		if ($nextIndex && $tokens[$nextIndex]['code'] === T_EQUAL) {
			return true;
		}

		if ($nextIndex && $tokens[$nextIndex]['code'] === T_CLOSE_SHORT_ARRAY) {
			$equalIndex = $file->findNext(Tokens::$emptyTokens, $nextIndex + 1, $nextIndex + 3, true, null, true);
			if ($equalIndex && $tokens[$equalIndex]['code'] === T_EQUAL) {
				return true;
			}
		}

		$prevIndex = $file->findPrevious(Tokens::$emptyTokens, $result['index'] - 1, $result['index'] - 3, true, null, true);
		if ($prevIndex === false) {
			return false;
		}

		for ($i = $prevIndex; $i > 0; $i--) {
			$testIndex = $file->findPrevious(Tokens::$emptyTokens, $i, $i - 2, true, null, true);
			if (!$testIndex) {
				break;
			}
			if ($tokens[$testIndex]['code'] !== T_COMMA && $tokens[$testIndex]['code'] !== T_VARIABLE) {
				$prevIndex = $testIndex;

				break;
			}
		}

		if ($prevIndex && $tokens[$prevIndex]['code'] === T_OPEN_SHORT_ARRAY) {
			$closerIndex = $tokens[$prevIndex]['bracket_closer'];
			$nextIndex = $file->findNext(Tokens::$emptyTokens, $closerIndex + 1, $closerIndex + 3, true, null, true);
			if ($nextIndex && $tokens[$nextIndex]['code'] === T_EQUAL) {
				return true;
			}
		}

		return false;
	}

}
