<?php

namespace IdeHelper\Annotator;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\ComponentTrait;
use IdeHelper\Utility\App;
use RuntimeException;
use Throwable;

class ComponentAnnotator extends AbstractAnnotator {

	use ComponentTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if (substr($name, -9) !== 'Component') {
			return false;
		}

		$name = substr($name, 0, -9);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		/** @phpstan-var class-string<object>|null $className */
		$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'Controller/Component', 'Component');
		if (!$className) {
			return false;
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}
		$annotations = $this->buildAnnotations($className);

		if ($this->hasControllerAnnotation($content)) {
			$appControllerClass = (Configure::read('App.namespace') ?: 'App') . '\Controller\AppController';
			$annotations[] = new MethodAnnotation('\\' . $appControllerClass, 'getController()');
		}

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @phpstan-param class-string<object> $className
	 *
	 * @param string $className
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(string $className): array {
		$annotations = [];

		$componentAnnotations = $this->getComponentAnnotations($className);
		foreach ($componentAnnotations as $componentAnnotation) {
			$annotations[] = $componentAnnotation;
		}

		return $annotations;
	}

	/**
	 * @phpstan-param class-string<object> $className
	 *
	 * @param string $className
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getComponentAnnotations(string $className) {
		if ($this->_isAbstract($className)) {
			return [];
		}

		$controller = new Controller();
		try {
			$object = new $className(new ComponentRegistry($controller));
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping component annotations: ' . $e->getMessage());
			}

			return [];
		}

		$map = $this->invokeProperty($object, '_componentMap');

		if (empty($map)) {
			return [];
		}

		$annotations = [];
		foreach ($map as $name => $config) {
			$className = $this->findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $name);
		}

		return $annotations;
	}

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	protected function hasControllerAnnotation(string $content): bool {
		return strpos($content, '$this->getController()') !== false;
	}

}
