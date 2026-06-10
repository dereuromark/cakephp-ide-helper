<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;

/**
 * Declared properties can be annotated with configured inline `@var` types.
 */
class PropertyTypeAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (!str_contains($path, DS . 'src' . DS)) {
			return false;
		}

		$propertyTypeMap = $this->propertyTypeMap();
		if (!$propertyTypeMap) {
			return false;
		}

		foreach (array_keys($propertyTypeMap) as $property) {
			if (preg_match('#\$\s*' . preg_quote((string)$property, '#') . '\b#', $content)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$propertyTypeMap = $this->propertyTypeMap();
		$properties = $this->findProperties($this->content, $propertyTypeMap);
		if (!$properties) {
			return false;
		}

		$changed = false;
		foreach ($properties as $property => $line) {
			if ($this->annotatePropertyContent($path, $propertyTypeMap[$property], $line)) {
				$changed = true;
			}
		}

		return $changed;
	}

	/**
	 * @param string $path
	 * @param string $type
	 * @param int $line
	 * @return bool
	 */
	protected function annotatePropertyContent(string $path, string $type, int $line): bool {
		$lines = explode("\n", $this->content);
		$index = $line - 1;
		if (!isset($lines[$index])) {
			return false;
		}

		if ($this->hasDocBlockBefore($lines, $index)) {
			$this->reportSkipped($path);

			return false;
		}

		if (!preg_match('/^(\s*)/', $lines[$index], $matches)) {
			return false;
		}

		$indentation = $matches[1];
		$docBlock = [
			$indentation . '/**',
			$indentation . ' * @var ' . $type,
			$indentation . ' */',
		];

		array_splice($lines, $index, 0, $docBlock);
		$newContent = implode("\n", $lines);
		if ($newContent === $this->content) {
			$this->reportSkipped($path);

			return false;
		}

		$this->_counter[static::COUNT_ADDED] = 1;
		$this->displayDiff($this->content, $newContent);
		$this->storeFile($path, $newContent);
		$this->content = $newContent;

		$this->report();

		return true;
	}

	/**
	 * @param array<int, string> $lines
	 * @param int $index
	 * @return bool
	 */
	protected function hasDocBlockBefore(array $lines, int $index): bool {
		for ($i = $index - 1; $i >= 0; $i--) {
			$line = trim($lines[$i]);
			if ($line === '') {
				continue;
			}

			return str_ends_with($line, '*/');
		}

		return false;
	}

	/**
	 * @param string $content
	 * @param array<string, string> $propertyTypeMap
	 * @return array<string, int>
	 */
	protected function findProperties(string $content, array $propertyTypeMap): array {
		$properties = [];
		$lines = explode("\n", $content);
		foreach ($lines as $index => $line) {
			foreach (array_keys($propertyTypeMap) as $property) {
				if (!preg_match('#^\s*(?:public|protected|private)\s+(?:static\s+)?(?:readonly\s+)?(?:[^$;=]+\s+)?\$' . preg_quote((string)$property, '#') . '\b#', $line)) {
					continue;
				}

				$properties[(string)$property] = $index + 1;
			}
		}

		return array_reverse($properties);
	}

	/**
	 * @return array<string, string>
	 */
	protected function propertyTypeMap(): array {
		/** @var array<string, string> $propertyTypeMap */
		$propertyTypeMap = (array)Configure::read('IdeHelper.propertyTypeMap');

		return array_filter($propertyTypeMap);
	}

}
