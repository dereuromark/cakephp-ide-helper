<?php

namespace IdeHelper\Annotator;

use Cake\Routing\RouteBuilder;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use RuntimeException;

class RoutesAnnotator extends TemplateAnnotator {

	/**
	 * @param string $path Path to file.
	 * @throws \RuntimeException
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		if ($this->hasAnnotation($content)) {
			return false;
		}

		$annotations = $this->buildAnnotations($path, $content);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(string $path, string $content): array {
		$classes = [
			'routes' => RouteBuilder::class,
		];

		$annotations = [];
		foreach ($classes as $alias => $className) {
			$annotations[] = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className, '$' . $alias);
		}

		return $annotations;
	}

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	protected function hasAnnotation(string $content): bool {
		return (bool)preg_match('/\* @var .+\\\\RouteBuilder \\$routes/', $content);
	}

}
