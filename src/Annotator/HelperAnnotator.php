<?php

namespace IdeHelper\Annotator;

use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\HelperTrait;
use IdeHelper\Utility\App;
use RuntimeException;
use Throwable;

class HelperAnnotator extends AbstractAnnotator {

	use HelperTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if (substr($name, -6) !== 'Helper') {
			return false;
		}

		$name = substr($name, 0, -6);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);

		/** @phpstan-var class-string<object>|null $className */
		$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'View/Helper', 'Helper');
		if (!$className) {
			return false;
		}

		if ($this->_isAbstract($className)) {
			return false;
		}

		try {
			$helper = new $className(new View());
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping helper annotations: ' . $e->getMessage());
			}

			return false;
		}

		$helperMap = $this->invokeProperty($helper, '_helperMap');

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$annotations = $this->getHelperAnnotations($helperMap);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param array<string, array<string, mixed>> $helperMap
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getHelperAnnotations(array $helperMap): array {
		if (empty($helperMap)) {
			return [];
		}

		$helperAnnotations = [];
		foreach ($helperMap as $helper => $config) {
			$className = $this->findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$helperAnnotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $helper);
		}

		return $helperAnnotations;
	}

}
