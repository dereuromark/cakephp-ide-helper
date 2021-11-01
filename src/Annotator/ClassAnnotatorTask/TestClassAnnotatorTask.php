<?php

namespace IdeHelper\Annotator\ClassAnnotatorTask;

use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\UsesAnnotation;

/**
 * Classes that test a class in a magic-call way should automatically have `@uses` annotated.
 * By default:
 * - Controller tests
 * - Command tests
 *
 * Use Configure key `IdeHelper.testClassPatterns` to add more types and their regex pattern.
 */
class TestClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

	/**
	 * Deprecated: $content, use $this->content instead.
	 *
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function shouldRun(string $path, string $content): bool {
		if (strpos($path, DS . 'tests' . DS . 'TestCase' . DS) === false) {
			return false;
		}

		$defaultTypes = (array)Configure::read('IdeHelper.testClassPatterns');
		$types = $defaultTypes + [
			'Controller' => '#\bclass .+ControllerTest extends\b#',
			'Command' => '#\bclass .+CommandTest extends\b#',
		];
		$typeList = implode('|', array_keys($types));

		if (!preg_match('#^namespace .+\\\\Test\\\\TestCase\\\\(' . $typeList . ')\b#m', $content)) {
			return false;
		}
		if (!$this->matchesType($content, $types)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $content
	 * @param array<string> $types
	 * @return bool
	 */
	protected function matchesType($content, array $types) {
		foreach ($types as $type => $pattern) {
			if (preg_match($pattern, $content)) {
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
		$class = $this->getTestedClass($this->content);
		if (!$class) {
			return false;
		}

		$annotations = $this->buildUsesAnnotations([$class]);

		return $this->annotateContent($path, $this->content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function getTestedClass(string $content): ?string {
		preg_match('#namespace (.+);#', $content, $matches);
		if (!$matches) {
			return null;
		}

		$namespace = str_replace('\\Test\\TestCase\\', '\\', $matches[1]);

		preg_match('#\bclass (.+)Test extends#', $content, $matches);
		if (!$matches) {
			return null;
		}
		$className = $matches[1];

		$fullClassName = $namespace . '\\' . $className;
		if (!class_exists($fullClassName)) {
			return null;
		}

		return $fullClassName;
	}

	/**
	 * @param array<string> $classes
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildUsesAnnotations(array $classes): array {
		$annotations = [];

		foreach ($classes as $className) {
			$annotations[] = AnnotationFactory::createOrFail(UsesAnnotation::TAG, '\\' . $className);
		}

		return $annotations;
	}

}
