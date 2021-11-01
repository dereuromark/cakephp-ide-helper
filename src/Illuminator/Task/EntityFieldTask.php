<?php

namespace IdeHelper\Illuminator\Task;

use Cake\Core\Configure;
use Cake\Utility\Inflector;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotation\PropertyReadAnnotation;
use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Reads the annotated properties of an entity and adds the constants based on those.
 */
class EntityFieldTask extends AbstractTask {

	use FileTrait;

	/**
	 * @var string
	 */
	public const PREFIX = 'FIELD_';

	/**
	 * @var array<string, mixed>
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
	public function shouldRun(string $path): bool {
		return (bool)preg_match('#' . preg_quote(DS) . 'Model' . preg_quote(DS) . 'Entity' . preg_quote(DS) . '.+\\.php$#', $path);
	}

	/**
	 * @param string $content
	 * @param string $path Path to file.
	 * @return string
	 */
	public function run(string $content, string $path): string {
		$file = $this->getFile('', $content);

		$classIndex = $file->findNext(T_CLASS, 0);
		if (!$classIndex) {
			return $content;
		}

		$tokens = $file->getTokens();

		$fields = $this->getFields($file, $classIndex);

		$existingConstants = $this->getFieldConstants($tokens, $tokens[$classIndex]['scope_opener'], $tokens[$classIndex]['scope_closer']);
		if ($existingConstants) {
			$fields = array_diff_key($fields, $existingConstants);
			$existingConstant = array_pop($existingConstants);
			$index = $existingConstant['index'];
			$addToExisting = true;
		} else {
			$index = $file->findPrevious(T_WHITESPACE, $tokens[$classIndex]['scope_closer'] + -1, $tokens[$classIndex]['scope_opener'], true);
			if ($index === false) {
				$index = $tokens[$classIndex]['scope_opener'];
			}
			$addToExisting = false;
		}

		return $this->addClassConstants($file, $fields, $index, $addToExisting, 0) ?: $content;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $classIndex
	 * @return array<string, array<string, mixed>>
	 */
	protected function getFields(File $file, int $classIndex): array {
		$tokens = $file->getTokens();

		$docBlockCloseTagIndex = $this->findDocBlockCloseTagIndex($file, $classIndex);
		if (!$docBlockCloseTagIndex || empty($tokens[$docBlockCloseTagIndex]['comment_opener'])) {
			return [];
		}

		$fields = [];
		for ($i = $tokens[$docBlockCloseTagIndex]['comment_opener'] + 1; $i < $docBlockCloseTagIndex; $i++) {

			if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
				continue;
			}
			if ($tokens[$i]['content'] !== PropertyAnnotation::TAG && $tokens[$i]['content'] !== PropertyReadAnnotation::TAG) {
				continue;
			}

			$pieces = explode(' ', $tokens[$i + 2]['content']);
			if (count($pieces) < 2) {
				continue;
			}
			$field = mb_substr($pieces[1], 1);
			if (strpos($field, ' ') === 0 || strpos($field, '_') === 0) {
				continue;
			}
			// We also skip camelCase as those are not the convention
			if (Inflector::underscore($field) !== $field) {
				continue;
			}

			$fields[$field] = [
				'name' => $field,
				'constant' => static::PREFIX . mb_strtoupper($field),
				'index' => $i,
			];
		}

		return $fields;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index First functional code after docblock
	 *
	 * @return int|null
	 */
	protected function findDocBlockCloseTagIndex(File $file, int $index): ?int {
		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $index - 1, null, true);
		if (!$prevCode) {
			return null;
		}

		return $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $index - 1, $prevCode) ?: null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<array<string, mixed>> $fields
	 * @param int $index Index of first token of previous line
	 * @param bool $addToExisting
	 * @param int $level
	 * @return string|null
	 */
	protected function addClassConstants(File $file, array $fields, $index, $addToExisting, $level = 1) {
		if (!$fields) {
			return null;
		}

		$tokens = $file->getTokens();

		$line = $tokens[$index]['line'];

		$i = $index;
		while ($tokens[$i + 1]['line'] === $line) {
			$i++;
		}

		$lastTokenOfLastLine = $i;

		$whitespace = '';
		$firstOfLine = $index;
		while ($tokens[$firstOfLine - 1]['line'] === $tokens[$index]['line']) {
			$firstOfLine--;
			$whitespace .= $tokens[$firstOfLine]['content'];
		}
		if ($level < 1) {
			$whitespace = Configure::read('IdeHelper.illuminatorIndentation') ?? "\t";
		}

		$beginIndex = $lastTokenOfLastLine;
		$visibility = '';
		if ($this->visibility()) {
			$visibility = 'public ';
		}

		$fixer = $this->getFixer($file);

		$fixer->beginChangeset();

		if (!$addToExisting) {
			$fixer->addNewline($beginIndex);
		}

		foreach ($fields as $field) {
			$fixer->addContent($beginIndex, $whitespace . $visibility . 'const ' . $field['constant'] . ' = \'' . $field['name'] . '\';');
			$fixer->addNewline($beginIndex);
		}

		$fixer->endChangeset();

		return $fixer->getContents();
	}

	/**
	 * @param array<array<string, mixed>> $tokens
	 * @param int $startIndex
	 * @param int $endIndex
	 * @return array<string, array<string, mixed>>
	 */
	protected function getFieldConstants(array $tokens, $startIndex, $endIndex) {
		$constants = [];

		for ($i = $startIndex + 1; $i < $endIndex; $i++) {
			if ($tokens[$i]['code'] !== T_CONST) {
				continue;
			}
			$index = $i + 1;
			if ($tokens[$index]['code'] === T_WHITESPACE) {
				$index++;
			}
			if ($tokens[$index]['code'] !== T_STRING) {
				continue;
			}

			$constant = $tokens[$index]['content'];

			$pos = strpos($constant, '_') ?: 0;
			$prefix = substr($constant, 0, $pos) ?: '';
			if ($prefix . '_' !== static::PREFIX) {
				continue;
			}

			$field = substr($constant, $pos + 1);
			$field = strtolower($field);

			$constants[$field] = [
				'index' => $i,
				'prefix' => $prefix,
				'name' => $field,
				'constant' => $constant,
			];
		}

		return $constants;
	}

	/**
	 * If visibility "public" should be used, for PHP 7.1+ only.
	 *
	 * @return bool
	 */
	protected function visibility(): bool {
		if ($this->_visibility !== null) {
			return $this->_visibility;
		}

		$visConfig = $this->getConfig('visibility') ?? true;

		return $this->_visibility = $visConfig;
	}

}
