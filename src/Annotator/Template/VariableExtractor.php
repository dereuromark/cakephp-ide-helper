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
	 * @return array
	 */
	public function extract(File $file) {
		$vars = $this->collect($file);

		$result = [];
		foreach ($vars as $var) {
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
	 * @param array $token
	 * @param int $index
	 * @return array
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
	 * @param array $result
	 * @return string|null
	 */
	protected function getVarType(File $file, array $result) {
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(Tokens::$emptyTokens, $result['index'] + 1, $result['index'] + 3, true, null, true);
		if ($nextIndex && $tokens[$nextIndex]['code'] === T_OBJECT_OPERATOR) {
			return 'object';
		}

		$prevIndex = $file->findPrevious(Tokens::$emptyTokens, $result['index'] - 1, $result['index'] - 3, true, null, true);
		if ($prevIndex && in_array($tokens[$prevIndex]['code'], [T_ECHO, T_OPEN_TAG_WITH_ECHO], true)) {
			return 'string';
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array $result
	 * @return string|null
	 */
	protected function getExcludeReason(File $file, array $result) {
		if ($this->isLoopVar($file, $result)) {
			return 'Declared in loop';
		}

		if ($this->isAssignment($file, $result)) {
			return 'Assignment';
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array $result
	 * @return bool
	 */
	protected function isLoopVar(File $file, array $result) {
		$tokens = $file->getTokens();

		$prevIndex = $file->findPrevious(Tokens::$emptyTokens, $result['index'] - 1, $result['index'] - 3, true, null, true);
		if ($prevIndex && in_array($tokens[$prevIndex]['code'], [T_AS], true)) {
			return true;
		}

		$nextIndex = $file->findNext(Tokens::$emptyTokens, $result['index'] + 1, $result['index'] + 3, true, null, true);
		if ($nextIndex && $tokens[$nextIndex]['code'] === T_DOUBLE_ARROW) {
			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array $result
	 * @return bool
	 */
	protected function isAssignment(File $file, array $result)
	{
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(Tokens::$emptyTokens, $result['index'] + 1, $result['index'] + 3, true, null, true);
		if ($nextIndex && $tokens[$nextIndex]['code'] === T_EQUAL) {
			return true;
		}

		return false;
	}

}
