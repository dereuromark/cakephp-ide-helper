<?php

namespace IdeHelper\Annotator\CallbackAnnotatorTask;

use Cake\Core\Exception\CakeException;
use Cake\Utility\Inflector;
use IdeHelper\Annotation\SeeAnnotation;
use PHP_CodeSniffer\Files\File;

/**
 * Fix up entity virtual field methods to have at least the $property linked.
 */
class VirtualFieldCallbackAnnotatorTask extends AbstractCallbackAnnotatorTask implements CallbackAnnotatorTaskInterface {

	/**
	 * @var array<string, string>
	 */
	protected $methods = [
		'_get' => '#_get[A-Z]\w+#',
		'_set' => '#_set[A-Z]\w+#',
	];

	/**
	 * @param string $path
	 * @return bool
	 */
	public function shouldRun(string $path): bool {
		if (!preg_match('#/Model/Entity/\w+\.php$#', $path)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$file = $this->getFile($path, $this->content);

		$methods = $this->getMethods($file);
		$namespace = $this->getNamespace($file);
		$class = $this->getClass($file);

		foreach ($methods as $index => $method) {
			if (!$this->isVirtualField($method)) {
				unset($methods[$index]);

				continue;
			}

			$method['see'] = $namespace . '\\' . $class . '::$' . Inflector::underscore(substr($method['name'], 4));

			if (!$this->needsUpdate($file, $index, $method)) {
				unset($methods[$index]);

				continue;
			}

			$methods[$index] = $method;
		}

		if (!$methods) {
			return false;
		}

		return $this->annotateMethods($path, $file, $methods);
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
			$replacingAnnotations = $method['annotations'] ?? [];
			foreach ($replacingAnnotations as $annotation) {
				$fixer->replaceToken($annotation->getIndex(), $annotation->build());
				$this->_counter[static::COUNT_UPDATED]++;
			}

			/** @var \IdeHelper\Annotation\LinkAnnotation|null $addingAnnotation */
			$addingAnnotation = $method['annotation'] ?? [];
			if ($addingAnnotation) {
				$endIndex = $method['docBlockEnd'];

				$indentation = $this->indentation($file, $endIndex);
				$fixer->addContentBefore($endIndex, '* @see ' . $addingAnnotation->build() . PHP_EOL . $indentation);
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
	 * @param array<string, mixed> $method
	 * @return bool
	 */
	protected function needsUpdate(File $file, int $index, array &$method): bool {
		if (empty($method['docBlockStart']) || empty($method['docBlockEnd'])) {
			return false;
		}

		$annotations = $this->parseExistingAnnotations($file, $method['docBlockEnd'], ['@see']);

		$expectedAnnotation = new SeeAnnotation($method['see']);
		if (empty($annotations) || $annotations[0]->getType() !== $method['see']) {
			$method['annotation'] = $expectedAnnotation;

			return true;
		}

		/** @var \IdeHelper\Annotation\LinkAnnotation $currentAnnotation */
		$currentAnnotation = $annotations[0];
		if ($currentAnnotation->getType() === $expectedAnnotation->getType()) {
			return false;
		}

		$currentAnnotation->replaceWith($expectedAnnotation);
		$currentAnnotation->setInUse();

		$method['annotations'] = [
			$currentAnnotation,
		];

		return true;
	}

	/**
	 * @param array<string, mixed> $method
	 *
	 * @return bool
	 */
	protected function isVirtualField(array $method): bool {
		foreach ($this->methods as $regex) {
			if (preg_match($regex, $method['name'])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return string
	 */
	protected function getNamespace(File $file): string {
		$namespaceIndex = $file->findNext(T_NAMESPACE, 0);
		$startIndex = $file->findNext(T_WHITESPACE, $namespaceIndex + 1, null, true);
		$endIndex = $file->findNext(T_SEMICOLON, $namespaceIndex + 1);

		if (!$namespaceIndex || !$endIndex) {
			throw new CakeException('File does not seem to be a valid entity class');
		}

		$tokens = $file->getTokens();
		$elements = ['\\'];
		for ($i = $startIndex; $i < $endIndex; $i++) {
			$elements[] = $tokens[$i]['content'];
		}

		return implode('', $elements);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return string
	 */
	protected function getClass(File $file): string {
		$classIndex = $file->findNext(T_CLASS, 0);
		$classNameIndex = $file->findNext(T_WHITESPACE, $classIndex + 1, null, true);

		$tokens = $file->getTokens();
		if (!$classNameIndex || empty($tokens[$classNameIndex])) {
			throw new CakeException('File does not seem to be a valid entity class');
		}

		return $tokens[$classNameIndex]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $endIndex
	 *
	 * @return string
	 */
	protected function indentation(File $file, int $endIndex): string {
		$tokens = $file->getTokens();
		$indentationElements = [];
		for ($i = $endIndex - 1; $i > 0; $i--) {
			if ($tokens[$i]['line'] !== $tokens[$endIndex]['line']) {
				break;
			}

			$indentationElements[] = $tokens[$i]['content'];
		}

		return implode('', $indentationElements);
	}

}
