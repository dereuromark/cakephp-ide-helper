<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace IdeHelper\Annotator\Traits;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * Common functionality around doc block parsing and writing.
 */
trait DocBlockTrait {

	/**
	 * @param string $tagName tag name
	 * @param string $tagComment tag comment
	 *
	 * @return \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode
	 */
	protected static function getValueNode(string $tagName, string $tagComment): PhpDocTagValueNode {
		static $phpDocParser;
		if (!$phpDocParser) {
			$constExprParser = new ConstExprParser();
			$phpDocParser = new PhpDocParser(new TypeParser($constExprParser), $constExprParser);
		}

		static $phpDocLexer;
		if (!$phpDocLexer) {
			$phpDocLexer = new Lexer();
		}

		return $phpDocParser->parseTagValue(new TokenIterator($phpDocLexer->tokenize($tagComment)), $tagName);
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode $valueNode
	 *
	 * @return array<string>
	 */
	protected function valueNodeParts(PhpDocTagValueNode $valueNode): array {
		if ($valueNode instanceof MethodTagValueNode) {
			$types = [$valueNode->returnType];
		} elseif ($valueNode instanceof GenericTagValueNode) {
			$types = [$valueNode];
		} elseif ($valueNode->type instanceof UnionTypeNode) {
			$types = $valueNode->type->types;
		} else {
			$types = [$valueNode->type];
		}

		$result = [];
		foreach ($types as $type) {
			$result[] = (string)$type;
		}

		return $result;
	}

	/**
	 * @param array<string> $parts
	 * @param \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode $valueNode
	 *
	 * @return string
	 */
	protected function stringifyValueNode(array $parts, PhpDocTagValueNode $valueNode): string {
		if ($valueNode instanceof ParamTagValueNode) {
			return trim(sprintf(
				'%s %s%s %s',
				implode('|', $parts),
				$valueNode->isVariadic ? '...' : '',
				$valueNode->parameterName,
				$valueNode->description,
			));
		}
		if ($valueNode instanceof ReturnTagValueNode) {
			return trim(sprintf(
				'%s%s',
				implode('|', $parts),
				$valueNode->description,
			));
		}
		if ($valueNode instanceof MethodTagValueNode) {
			return trim(sprintf(
				'%s %s() %s',
				implode('|', $parts),
				$valueNode->methodName,
				$valueNode->description,
			));
		}
		if ($valueNode instanceof VarTagValueNode) {
			return trim(sprintf(
				'%s %s%s',
				implode('|', $parts),
				$valueNode->variableName,
				$valueNode->description,
			));
		}
		if ($valueNode instanceof ThrowsTagValueNode) {
			return trim(sprintf(
				'%s %s',
				implode('|', $parts),
				$valueNode->description,
			));
		}

		return trim(implode('|', $parts));
	}

	/**
	 * Looks for either `@inheritDoc` or `{@inheritDoc}`.
	 * Also allows `@inheritdoc` or `{@inheritdoc}` aliases.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $docBlockStartIndex
	 * @param int $docBlockEndIndex
	 * @param string $needle
	 *
	 * @return bool
	 */
	protected function hasInheritDoc(File $phpCsFile, int $docBlockStartIndex, int $docBlockEndIndex, string $needle = '@inheritDoc'): bool {
		$tokens = $phpCsFile->getTokens();

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; ++$i) {
			if (empty($tokens[$i]['content'])) {
				continue;
			}
			$content = $tokens[$i]['content'];
			$pos = stripos($content, $needle);
			if ($pos === false) {
				continue;
			}

			if ($pos && strpos($needle, '@') === 0 && substr($content, $pos - 1, $pos) === '{') {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks for ...<...>.
	 *
	 * @param array<string> $docBlockTypes
	 *
	 * @return bool
	 */
	protected function containsIterableSyntax(array $docBlockTypes): bool {
		foreach ($docBlockTypes as $docBlockType) {
			if (strpos($docBlockType, '<') !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array<\PHPStan\PhpDocParser\Ast\Type\TypeNode|string> $typeNodes type nodes
	 *
	 * @return string
	 */
	protected function renderUnionTypes(array $typeNodes): string {
		$string = (string)preg_replace(
			['/ ([\|&]) /', '/<\(/', '/\)>/', '/\), /', '/, \(/'],
			['${1}', '<', '>', ', ', ', '],
			implode('|', $typeNodes),
		);

		if (substr($string, 0, 1) === '(' && substr($string, -1, 1) === ')') {
			$string = substr($string, 1, -1);
		}

		return $string;
	}

}
