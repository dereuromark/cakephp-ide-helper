<?php

namespace IdeHelper\Annotator\CallbackAnnotatorTask;

use Cake\Utility\Inflector;
use IdeHelper\Annotation\LinkAnnotation;
use PHP_CodeSniffer\Files\File;

/**
 * Fix up entity virtual field methods to have at least the $property linked.
 */
class VirtualFieldCallbackAnnotatorTask extends AbstractCallbackAnnotatorTask implements CallbackAnnotatorTaskInterface {

	/**
	 * @var array<string, string>
	 */
	protected $methods = [
		'_get' => '#\bprotected function (_get\w+)\(\)#',
		'_set' => '#\bprotected function (_set\w+)\(\)#',
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

		foreach ($methods as $index => $method) {
			if (!$this->isVirtualField($method)) {
				unset($methods[$index]);

				continue;
			}

			$method['link'] = '$' . Inflector::underscore(substr($method['name'], 4));

			if (!$this->needsUpdate($file, $index, $method)) {
				unset($methods[$index]);

				continue;
			}

			$methods[$index] = $method;
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

				//$file->findFirstOnLine();

				//dd($file->getTokens()[$endIndex]);
				$fixer->addContentBefore($endIndex, '* @link ' . $addingAnnotation->build() . PHP_EOL . '	 ');
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

		$annotations = $this->parseExistingAnnotations($file, $method['docBlockEnd'], ['@link']);

		$expectedAnnotation = new LinkAnnotation($method['link']);

		if (empty($annotations) || $annotations[0]->getType() !== $method['link']) {
			$method['annotation'] = $expectedAnnotation;

			return true;
		}

		/** @var \IdeHelper\Annotation\LinkAnnotation $currentAnnotation */
		$currentAnnotation = $annotations[0];
		if ($currentAnnotation->getType() === $expectedAnnotation->getType()) {
			return false;
		}

		$currentAnnotation->replaceWith($expectedAnnotation);

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
			if (preg_match($regex, $method['name']) !== false) {
				return true;
			}
		}

		return false;
	}

}
