<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotator\Traits\ComponentTrait;
use IdeHelper\Annotator\Traits\ModelTrait;
use IdeHelper\Utility\App;
use IdeHelper\Utility\GenericString;
use RuntimeException;
use Throwable;

class ControllerAnnotator extends AbstractAnnotator {

	use ComponentTrait;
	use ModelTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if (!str_ends_with($className, 'Controller')) {
			return false;
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}
		$primaryModelName = $this->getPrimaryModelClass($content, $className, $path);

		$usedModels = $this->getUsedModels($content);
		if ($primaryModelName) {
			$usedModels[] = $primaryModelName;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->getModelAnnotations($usedModels, $content);

		$componentAnnotations = $this->getComponentAnnotations($className, $path);
		foreach ($componentAnnotations as $componentAnnotation) {
			$annotations[] = $componentAnnotation;
		}

		$paginationAnnotations = $this->getPaginationAnnotations($content, $primaryModelName);
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

		if (preg_match('/\bprotected \?string \$defaultTable = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return $matches[1];
		}

		if (preg_match('/\bprotected \?string \$defaultTable = \'\';/i', $content, $matches)) {
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
	 * @param string $className
	 * @param string $path
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getComponentAnnotations(string $className, string $path): array {
		try {
			$map = $this->getUsedComponents($className, $path);
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
			if (str_starts_with($className, 'Cake\\')) {
				continue;
			}

			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $component);
		}

		return $annotations;
	}

	/**
	 * @param string $className
	 * @param string $path
	 *
	 * @return array<string>
	 */
	protected function getUsedComponents(string $className, string $path): array {
		$plugin = $className !== 'AppController' ? $this->getConfig(static::CONFIG_PLUGIN) : null;
		$prefix = $this->getPrefix($className, $path);

		/** @phpstan-var class-string<object>|null $fullClassName */
		$fullClassName = App::className(($plugin ? $plugin . '.' : '') . $className, 'Controller' . $prefix);
		if (!$fullClassName) {
			return [];
		}

		if ($this->_isAbstract($fullClassName)) {
			return [];
		}

		$request = new ServerRequest(['url' => 'justfortesting']);
		/** @var \App\Controller\AppController $controller */
		$controller = new $fullClassName($request);

		$components = [];
		foreach ($controller->components()->loaded() as $component) {
			$components[$component] = get_class($controller->components()->get($component));
		}

		if ($className === 'AppController') {
			return $components;
		}

		$appControllerComponents = $this->getUsedComponents('AppController', $path);
		$components = array_diff_key($components, $appControllerComponents);

		return $components;
	}

	/**
	 * @param string $content
	 * @param string|null $primaryModelName
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getPaginationAnnotations(string $content, ?string $primaryModelName): array {
		$entities = $this->extractPaginateEntities($content, $primaryModelName);
		if (!$entities) {
			return [];
		}

		$resultSetInterfaceCollection = GenericString::generate(implode('|', $entities), '\\' . ResultSetInterface::class);

		$settingsType = 'array';
		if (Configure::read('IdeHelper.genericsInParam')) {
			$settingsType = 'array<string, mixed> ';
		}

		$annotations = [AnnotationFactory::createOrFail(MethodAnnotation::TAG, $resultSetInterfaceCollection, 'paginate(\Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object = null, ' . $settingsType . ' $settings = [])')];

		return $annotations;
	}

	/**
	 * @param string $content
	 * @param string|null $primaryModelName
	 *
	 * @return array<string>
	 */
	protected function extractPaginateEntities(string $content, ?string $primaryModelName): array {
		$models = [];

		preg_match_all('/\$this->paginate\(\)/i', $content, $matches);
		if (!empty($matches[0]) && $primaryModelName) {
			$models[] = $primaryModelName;
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
			$entityClassName = $this->getEntity($model, $primaryModelName);

			$fullClassName = '\\' . ltrim($entityClassName, '\\');
			if (in_array($fullClassName, $result, true)) {
				continue;
			}
			$result[] = $fullClassName;
		}

		return $result;
	}

	/**
	 * @param string $modelName
	 * @param string|null $primaryModelName Can be plugin dot syntaxed
	 *
	 * @return string
	 */
	protected function getEntity(string $modelName, ?string $primaryModelName): string {
		if ($this->getConfig(static::CONFIG_PLUGIN) && $modelName !== $primaryModelName && !strpos($modelName, '.')) {
			$modelName = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelName;
		}

		try {
			$table = TableRegistry::getTableLocator()->get($modelName);
			$entityClassName = $table->getEntityClass();
		} catch (Throwable $exception) {
			$plugin = null;
			if (str_contains($modelName, '.')) {
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
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$prefix = $this->getPrefix($className, $path);

		$fullClassName = App::className(($plugin ? $plugin . '.' : '') . $className, 'Controller' . $prefix);
		if (!$fullClassName) {
			return null;
		}

		try {
			/** @var \Cake\Controller\Controller $controller */
			$controller = new $fullClassName(new ServerRequest());
		} catch (Throwable $e) {
			$this->_io->warn('   Could not look up model class for ' . $fullClassName . ': ' . $e->getMessage());

			return null;
		}

		$modelClass = $this->invokeProperty($controller, 'defaultTable');
		if (!$modelClass) {
			return null;
		}

		if ($this->getConfig(static::CONFIG_PLUGIN) && !str_contains($modelClass, '.')) {
			$modelClass = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelClass;
		}

		$fullClassName = App::className($modelClass, 'Model/Table', 'Table');
		if (!$fullClassName) {
			return null;
		}

		return $modelClass;
	}

	/**
	 * Namespace prefix for controllers.
	 *
	 * @param string $className
	 * @param string $path
	 *
	 * @return string
	 */
	protected function getPrefix(string $className, string $path): string {
		preg_match('#/Controller/(\w+)/' . $className . '\.php#', $path, $matches);
		$prefix = '';
		if ($matches) {
			$prefix = '/' . $matches[1];
		}

		return $prefix;
	}

}
