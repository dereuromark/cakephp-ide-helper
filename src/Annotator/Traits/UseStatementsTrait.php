<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace IdeHelper\Annotator\Traits;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

trait UseStatementsTrait {

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 *
	 * @return array<string, array<string, mixed>>
	 */
	protected function getUseStatements(File $phpcsFile): array {
		$tokens = $phpcsFile->getTokens();

		$statements = [];
		foreach ($tokens as $index => $token) {
			if ($token['code'] !== T_USE || $token['level'] > 0) {
				continue;
			}

			$useStatementStartIndex = $phpcsFile->findNext(Tokens::$emptyTokens, $index + 1, null, true);

			// Ignore function () use ($foo) {}
			if ($tokens[$useStatementStartIndex]['content'] === '(') {
				continue;
			}

			$semicolonIndex = $phpcsFile->findNext(T_SEMICOLON, $useStatementStartIndex + 1);
			$useStatementEndIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, $semicolonIndex - 1, null, true);

			$statement = '';
			for ($i = $useStatementStartIndex; $i <= $useStatementEndIndex; $i++) {
				$statement .= $tokens[$i]['content'];
			}

			// Another sniff takes care of that, we just ignore then.
			if ($this->isMultipleUseStatement($statement)) {
				continue;
			}

			$statementParts = preg_split('/\s+as\s+/i', $statement) ?: [];

			if (count($statementParts) === 1) {
				$fullName = $statement;
				$statementParts = explode('\\', $fullName);
				$shortName = end($statementParts);
				$alias = null;
			} else {
				$fullName = $statementParts[0];
				$alias = $statementParts[1];
				$statementParts = explode('\\', $fullName);
				$shortName = end($statementParts);
			}

			$shortName = trim($shortName);
			$fullName = trim($fullName);
			$key = $alias ?: $shortName;

			$statements[$key] = [
				'alias' => $alias,
				'end' => $semicolonIndex,
				'statement' => $statement,
				'fullName' => ltrim($fullName, '\\'),
				'shortName' => $shortName,
				'start' => $index,
			];
		}

		return $statements;
	}

	/**
	 * @param string $statementContent
	 *
	 * @return bool
	 */
	protected function isMultipleUseStatement(string $statementContent): bool {
		if (str_contains($statementContent, ',')) {
			return true;
		}

		return false;
	}

}
