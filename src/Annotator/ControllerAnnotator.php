<?php

namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Exception;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\ComponentTrait;
use Throwable;

class ControllerAnnotator extends AbstractAnnotator {

	use ComponentTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if (substr($className, -10) !== 'Controller') {
			return false;
		}

		$content = file_get_contents($path);
		$primaryModelClass = $this->_getPrimaryModelClass($content, $className, $path);

		$usedModels = $this->_getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		$componentAnnotations = $this->_getComponentAnnotations($className);
		foreach ($componentAnnotations as $componentAnnotation) {
			$annotations[] = $componentAnnotation;
		}

		$paginationAnnotations = $this->_getPaginationAnnotations($content, $primaryModelClass);
		foreach ($paginationAnnotations as $paginationAnnotation) {
			$annotations[] = $paginationAnnotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 * @param string $className
	 * @param string $path
	 * @return string|null
	 */
	protected function _getPrimaryModelClass($content, $className, $path) {
		if ($className === 'AppController' || preg_match('#[a-z0-9]AppController$#', $className)) {
			return null;
		}

		$dynamicallyFoundModelClass = $this->_findModelClass($className, $path);
		if ($dynamicallyFoundModelClass !== null) {
			return $dynamicallyFoundModelClass !== false ? $dynamicallyFoundModelClass : null;
		}

		if (preg_match('/\bpublic \$modelClass = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return $matches[1];
		}

		if (preg_match('/\bpublic \$modelClass = false;/i', $content, $matches)) {
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
	protected function _getUsedModels($content) {
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
	protected function _getComponentAnnotations($controllerName) {
		try {
			$map = $this->_getUsedComponents($controllerName);
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping component annotations: ' . $e->getMessage());
			}
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
	protected function _getUsedComponents($controllerName) {
		$plugin = $controllerName !== 'AppController' ? $this->getConfig(static::CONFIG_PLUGIN) : null;
		$className = App::className(($plugin ? $plugin . '.' : '') . $controllerName, 'Controller');
		if (!$className) {
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

		$appControllerComponents = $this->_getUsedComponents('AppController');
		$components = array_diff_key($components, $appControllerComponents);

		return $components;
	}

	/**
	 * @param string $content
	 * @param string $primaryModelClass
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function _getPaginationAnnotations($content, $primaryModelClass) {
		$entityTypehints = $this->_extractPaginateEntityTypehints($content, $primaryModelClass);
		if (!$entityTypehints) {
			return [];
		}

		$entityTypehints[] = '\Cake\Datasource\ResultSetInterface';

		$type = implode('|', $entityTypehints);

		$annotations = [AnnotationFactory::createOrFail(MethodAnnotation::TAG, $type, 'paginate($object = null, array $settings = [])')];

		return $annotations;
	}

	/**
	 * @param string $content
	 * @param string $primaryModelClass
	 *
	 * @return string[]
	 */
	protected function _extractPaginateEntityTypehints($content, $primaryModelClass) {
		$models = [];

		preg_match_all('/\$this->paginate\(\)/i', $content, $matches);
		if (!empty($matches[0])) {
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
	 * @param string $primaryModelClass Can be plugin dot syntaxed
	 *
	 * @return string
	 */
	protected function getEntity($modelName, $primaryModelClass) {
		if ($this->getConfig(static::CONFIG_PLUGIN) && $modelName !== $primaryModelClass && !strpos($modelName, '.')) {
			$modelName = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelName;
		}

		try {
			$table = TableRegistry::getTableLocator()->get($modelName);
			$entityClassName = $table->getEntityClass();
		} catch (Exception $exception) {
			$plugin = null;
			if (strpos($modelName, '.') !== false) {
				list($plugin, $modelName) = explode('.', $modelName, 2);
			}
			$entity = Inflector::singularize($modelName);
			$fullClassName = ($plugin ?: Configure::read('App.namespace', 'App')) . '\\Model\\Entity\\' . $entity;
			if (class_exists($fullClassName)) {
				return '\\' . $fullClassName;
			}

			return '\Cake\ORM\Entity';
		}

		return $entityClassName;
	}

	/**
	 * @param string $className
	 * @param string $path
	 *
	 * @return string|false|null
	 */
	protected function _findModelClass($className, $path) {
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
		} catch (Exception $e) {
			$this->_io->warn('   Could not look up model class for ' . $fullClassName . ': ' . $e->getMessage());
			return null;
		} catch (Throwable $e) {
			$this->_io->warn('   Could not look up model class for ' . $fullClassName . ': ' . $e->getMessage());
			return null;
		}

		$modelClass = $controller->modelClass;
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
