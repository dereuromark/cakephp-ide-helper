<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\ComponentTrait;
use IdeHelper\Utility\App;
use Throwable;

class ControllerAnnotator extends AbstractAnnotator {

	use ComponentTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if (substr($className, -10) !== 'Controller') {
			return false;
		}

		$content = file_get_contents($path);
		$primaryModelClass = $this->getPrimaryModelClass($content, $className, $path);

		$usedModels = $this->getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->getModelAnnotations($usedModels, $content);

		$componentAnnotations = $this->getComponentAnnotations($className);
		foreach ($componentAnnotations as $componentAnnotation) {
			$annotations[] = $componentAnnotation;
		}

		$paginationAnnotations = $this->getPaginationAnnotations($content, $primaryModelClass);
		foreach ($paginationAnnotations as $paginationAnnotation) {
			$annotations[] = $paginationAnnotation;
		}

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 * @param string $className
	 * @param string $path
	 * @return string|null
	 */
	protected function getPrimaryModelClass(string $content, string $className, string $path): ?string {
		if ($className === 'AppController' || preg_match('#[a-z0-9]AppController$#', $className)) {
			return null;
		}

		$dynamicallyFoundModelClass = $this->findModelClass($className, $path);
		if ($dynamicallyFoundModelClass !== null) {
			return $dynamicallyFoundModelClass !== '' ? $dynamicallyFoundModelClass : null;
		}

		if (preg_match('/\bprotected \$modelClass = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return $matches[1];
		}

		if (preg_match('/\bprotected \$modelClass = \'\';/i', $content, $matches)) {
			return null;
		}

		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$modelName = substr($className, 0, -10);
		$modelClassName = ($plugin ?: Configure::read('App.namespace', 'App')) . '\\Model\\Table\\' . $modelName . 'Table';
		if (!class_exists($modelClassName)) {
			return null;
		}

		if ($modelName && $plugin) {
			$modelName = $plugin . '.' . $modelName;
		}

		return $modelName ?: null;
	}

	/**
	 * @param string $content
	 *
	 * @return string[]
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this->loadModel\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

	/**
	 * @param string $controllerName
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function getComponentAnnotations(string $controllerName): array {
		try {
			$map = $this->getUsedComponents($controllerName);
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping component annotations: ' . $e->getMessage());
			}
		}

		if (empty($map)) {
			return [];
		}

		$annotations = [];
		foreach ($map as $component => $className) {
			if (substr($className, 0, 5) === 'Cake\\') {
				continue;
			}

			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $component);
		}

		return $annotations;
	}

	/**
	 * @param string $controllerName
	 *
	 * @return string[]
	 */
	protected function getUsedComponents(string $controllerName): array {
		$plugin = $controllerName !== 'AppController' ? $this->getConfig(static::CONFIG_PLUGIN) : null;
		$className = App::className(($plugin ? $plugin . '.' : '') . $controllerName, 'Controller');
		if (!$className) {
			return [];
		}

		if ($this->_isAbstract($className)) {
			return [];
		}

		/** @var \App\Controller\AppController $controller */
		$controller = new $className();

		$components = [];
		foreach ($controller->components()->loaded() as $component) {
			$components[$component] = get_class($controller->components()->get($component));
		}

		if ($controllerName === 'AppController') {
			return $components;
		}

		$appControllerComponents = $this->getUsedComponents('AppController');
		$components = array_diff_key($components, $appControllerComponents);

		return $components;
	}

	/**
	 * @param string $content
	 * @param string|null $primaryModelClass
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function getPaginationAnnotations(string $content, ?string $primaryModelClass): array {
		$entityTypehints = $this->extractPaginateEntityTypehints($content, $primaryModelClass);
		if (!$entityTypehints) {
			return [];
		}

		$entityTypehints[] = '\\' . ResultSetInterface::class;

		$type = implode('|', $entityTypehints);

		$annotations = [AnnotationFactory::createOrFail(MethodAnnotation::TAG, $type, 'paginate($object = null, array $settings = [])')];

		return $annotations;
	}

	/**
	 * @param string $content
	 * @param string|null $primaryModelClass
	 *
	 * @return string[]
	 */
	protected function extractPaginateEntityTypehints(string $content, ?string $primaryModelClass): array {
		$models = [];

		preg_match_all('/\$this->paginate\(\)/i', $content, $matches);
		if (!empty($matches[0]) && $primaryModelClass) {
			$models[] = $primaryModelClass;
		}

		preg_match_all('/\$this->paginate\(\$this->([a-z]+)\)/i', $content, $matches);
		if (!empty($matches[1])) {
			$models = array_merge($models, $matches[1]);
		}

		if (!$models) {
			return [];
		}

		$result = [];
		foreach ($models as $model) {
			$entityClassName = $this->getEntity($model, $primaryModelClass);

			$typehint = '\\' . ltrim($entityClassName, '\\') . '[]';
			if (in_array($typehint, $result)) {
				continue;
			}
			$result[] = $typehint;
		}

		return $result;
	}

	/**
	 * @param string $modelName
	 * @param string|null $primaryModelClass Can be plugin dot syntaxed
	 *
	 * @return string
	 */
	protected function getEntity(string $modelName, ?string $primaryModelClass): string {
		if ($this->getConfig(static::CONFIG_PLUGIN) && $modelName !== $primaryModelClass && !strpos($modelName, '.')) {
			$modelName = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelName;
		}

		try {
			$table = TableRegistry::getTableLocator()->get($modelName);
			$entityClassName = $table->getEntityClass();
		} catch (Throwable $exception) {
			$plugin = null;
			if (strpos($modelName, '.') !== false) {
				[$plugin, $modelName] = explode('.', $modelName, 2);
			}
			$entity = Inflector::singularize($modelName);
			$fullClassName = ($plugin ?: Configure::read('App.namespace', 'App')) . '\\Model\\Entity\\' . $entity;
			if (class_exists($fullClassName)) {
				return $fullClassName;
			}

			return Entity::class;
		}

		return $entityClassName;
	}

	/**
	 * @param string $className
	 * @param string $path
	 *
	 * @return string|null
	 */
	protected function findModelClass(string $className, string $path): ?string {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '';
		preg_match('#/Controller/(\w+)/' . $className . '\.php#', $path, $matches);
		$prefix = null;
		if ($matches) {
			$prefix = '/' . $matches[1];
		}

		$fullClassName = App::className($plugin . $className, 'Controller' . $prefix);
		if (!$fullClassName) {
			return null;
		}

		try {
			/** @var \Cake\Controller\Controller $controller */
			$controller = new $fullClassName();
		} catch (Throwable $e) {
			$this->_io->warn('   Could not look up model class for ' . $fullClassName . ': ' . $e->getMessage());
			return null;
		}

		$modelClass = $this->invokeProperty($controller, 'modelClass');
		if (!$modelClass) {
			return null;
		}

		if ($this->getConfig(static::CONFIG_PLUGIN) && strpos($modelClass, '.') === false) {
			$modelClass = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelClass;
		}

		$className = App::className($modelClass, 'Model/Table', 'Table');
		if (!$className) {
			return null;
		}

		return $modelClass;
	}

}
