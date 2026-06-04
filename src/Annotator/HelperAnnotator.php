<?php

namespace IdeHelper\Annotator;

use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\ExtendsAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\HelperTrait;
use IdeHelper\Utility\App;
use ReflectionClass;
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
		if (!str_ends_with($name, 'Helper')) {
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

		/** @uses \Cake\View\Helper::helpers */
		$helperMap = $this->invokeProperty($helper, 'helpers');

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$annotations = $this->getHelperAnnotations($helperMap);
		$annotations = $this->addHelperExtends($annotations, $className);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * Emits `@extends \Cake\View\Helper<\Cake\View\View>` so PHPStan's
	 * `missingType.generics` stays clean on `Cake\View\Helper` (generic via
	 * `@template TView` since CakePHP 5.3). Gated by a runtime check on the
	 * parent's doc-block rather than a version constant, so it self-disables on
	 * older cores and never emits a bare/over-specified generic on a
	 * non-generic parent (which would trigger `generics.notGeneric`).
	 *
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 * @param class-string<object> $className
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function addHelperExtends(array $annotations, string $className): array {
		$parentClass = get_parent_class($className);
		if ($parentClass === false || !$this->parentSupportsGenerics($parentClass)) {
			return $annotations;
		}

		// Prepend so `@extends` sits at the top of the class doc-block, matching the
		// tag order enforced by php-collective/code-sniffer DocBlockTagOrderSniff.
		array_unshift(
			$annotations,
			AnnotationFactory::createOrFail(ExtendsAnnotation::TAG, '\\' . ltrim($parentClass, '\\') . '<\\' . View::class . '>'),
		);

		return $annotations;
	}

	/**
	 * Whether the parent helper declares template parameters and can therefore
	 * be parameterized via `@extends`.
	 *
	 * @param string $parentClass
	 * @return bool
	 */
	protected function parentSupportsGenerics(string $parentClass): bool {
		$fqcn = ltrim($parentClass, '\\');
		if (!class_exists($fqcn)) {
			return false;
		}

		$doc = (new ReflectionClass($fqcn))->getDocComment();
		if ($doc === false) {
			return false;
		}

		return preg_match('/^\s*\*\s*@template\s/m', $doc) === 1;
	}

	/**
	 * @param array<string, array<string, mixed>> $helperMap
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getHelperAnnotations(array $helperMap): array {
		if (!$helperMap) {
			return [];
		}

		$helperAnnotations = [];
		foreach ($helperMap as $helper => $config) {
			$className = $this->findClassName($config['className'] ?? $helper, !$this->getConfig(static::CONFIG_PLUGIN));
			if (!$className) {
				continue;
			}

			$helperAnnotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $helper);
		}

		return $helperAnnotations;
	}

}
