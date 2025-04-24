<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\AssociationCollection;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\ExtendsAnnotation;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Console\Io;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\GenericString;
use ReflectionClass;
use RuntimeException;
use Throwable;

class ModelAnnotator extends AbstractAnnotator {

	public const CLASS_TABLE = Table::class;

	/**
	 * @var string
	 */
	public const TABLE_BEHAVIORS = 'tableBehaviors';

	/**
	 * @var string
	 */
	public const BEHAVIOR_MIXIN = 'mixin';

	/**
	 * @var string
	 */
	public const BEHAVIOR_EXTENDS = 'extends';

	/**
	 * @var array<string, array<string, string>>
	 */
	protected array $_cache = [];

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 */
	public function __construct(Io $io, array $config) {
		parent::__construct($io, $config);

		/** @var string|bool|null $tableBehaviors */
		$tableBehaviors = Configure::read('IdeHelper.tableBehaviors');
		if ($tableBehaviors === true) {
			$tableBehaviors = [
				static::BEHAVIOR_MIXIN,
				static::BEHAVIOR_EXTENDS,
			];
		} elseif ($tableBehaviors === false) {
			$tableBehaviors = [];
		} elseif ($tableBehaviors === null) {
			$tableBehaviors = [
				static::BEHAVIOR_MIXIN,
			];
			if (version_compare(Configure::version(), '5.2.3', '>=')) {
				$tableBehaviors[] = static::BEHAVIOR_EXTENDS;
			}
		}

		$this->setConfig(static::TABLE_BEHAVIORS, (array)$tableBehaviors);
	}

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Table' || !str_ends_with($className, 'Table')) {
			return false;
		}

		$modelName = substr($className, 0, -5);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);

		$tableName = $plugin ? ($plugin . '.' . $modelName) : $modelName;
		try {
			/** @phpstan-var class-string<object> $tableClass */
			$tableClass = App::classNameOrFail($tableName, 'Model/Table', 'Table');
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: ' . $e->getMessage());
			}

			return false;
		}

		if ($this->_isAbstract($tableClass)) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: Abstract class');
			}

			return false;
		}

		$tableReflection = new ReflectionClass($tableClass);
		if (!$tableReflection->isInstantiable()) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: Not instantiable');
			}

			return false;
		}

		try {
			$table = TableRegistry::getTableLocator()->get($tableName);
			$schema = $table->getSchema();
			$behaviors = $this->getBehaviors($table);
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: ' . $e->getMessage());
			}

			return false;
		}

		$tableAssociations = $table->associations();
		try {
			$associations = $this->getAssociations($tableAssociations);
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping associations: ' . $e->getMessage());
			}
			$associations = [];
		}

		$entityClassName = $table->getEntityClass();
		$entityName = substr($entityClassName, strrpos($entityClassName, '\\') + 1);

		$parentClass = (string)get_parent_class($table);
		$resTable = $this->table($path, $entityName, $associations, $behaviors, $parentClass);
		$resEntity = $this->entity($entityClassName, $entityName, $schema, $tableAssociations);

		return $resTable || $resEntity;
	}

	/**
	 * @param string $path
	 * @param string $entityName
	 * @param array<string, mixed> $associations
	 * @param array<string> $behaviors
	 * @param string $parentClass
	 * @return bool
	 */
	protected function table(string $path, string $entityName, array $associations, array $behaviors, string $parentClass): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$behaviors += $this->parseLoadedBehaviors($content);
		$annotations = $this->buildAnnotations($associations, $entityName, $behaviors, $parentClass);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param array<string, mixed> $associations
	 * @param string $entity
	 * @param array<string> $behaviors
	 * @param string $parentClass
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(array $associations, string $entity, array $behaviors, string $parentClass): array {
		$namespace = $this->getConfig(static::CONFIG_NAMESPACE);
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $name => $className) {
				if (Configure::read('IdeHelper.assocsAsGenerics') === true) {
					$annotations[] = "@property \\{$type}<\\{$className}> \${$name}";
				} else {
					$annotations[] = "@property \\{$className}&\\{$type} \${$name}";
				}
			}
		}

		$fullClassName = "$namespace\\Model\\Entity\\$entity";
		if (class_exists($fullClassName)) {
			$fullClassName = '\\' . $fullClassName;
			$fullClassNameCollection = GenericString::generate($fullClassName);
			$entityInterface = '\\' . EntityInterface::class;
			$resultSetInterfaceCollection = GenericString::generate($fullClassName, '\\' . ResultSetInterface::class);

			if (Configure::read('IdeHelper.concreteEntitiesInParam')) {
				$entityInterface = $fullClassName;
			}

			$dataType = 'array';
			$optionsType = 'array';
			$iterable = 'iterable';
			if (Configure::read('IdeHelper.genericsInParam')) {
				$dataType = 'array<mixed>';
				$optionsType = 'array<string, mixed>';
				$iterable = "iterable<{$entityInterface}>";
			}

			/**
			 * Copied from Bake plugin's DocBlockHelper
			 *
			 * @link \Bake\View\Helper\DocBlockHelper::buildTableAnnotations()
			 */
			$annotations[] = "@method {$fullClassName} newEmptyEntity()";
			$annotations[] = "@method {$fullClassName} newEntity({$dataType} \$data, {$optionsType} \$options = [])";
			$annotations[] = "@method {$fullClassNameCollection} newEntities({$dataType} \$data, {$optionsType} \$options = [])";

			$annotations[] = "@method {$fullClassName} get(mixed \$primaryKey, array|string \$finder = 'all', \Psr\SimpleCache\CacheInterface|string|null \$cache = null, \Closure|string|null \$cacheKey = null, mixed ...\$args)";
			$annotations[] = "@method {$fullClassName} findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array \$search, ?callable \$callback = null, {$optionsType} \$options = [])";

			$annotations[] = "@method {$fullClassName} patchEntity({$entityInterface} \$entity, {$dataType} \$data, {$optionsType} \$options = [])";
			$annotations[] = "@method {$fullClassNameCollection} patchEntities({$iterable} \$entities, {$dataType} \$data, {$optionsType} \$options = [])";

			$annotations[] = "@method {$fullClassName}|false save({$entityInterface} \$entity, {$optionsType} \$options = [])";
			$annotations[] = "@method {$fullClassName} saveOrFail({$entityInterface} \$entity, {$optionsType} \$options = [])";

			$annotations[] = "@method {$resultSetInterfaceCollection}|false saveMany({$iterable} \$entities, {$optionsType} \$options = [])";
			$annotations[] = "@method {$resultSetInterfaceCollection} saveManyOrFail({$iterable} \$entities, {$optionsType} \$options = [])";

			$annotations[] = "@method {$resultSetInterfaceCollection}|false deleteMany({$iterable} \$entities, {$optionsType} \$options = [])";
			$annotations[] = "@method {$resultSetInterfaceCollection} deleteManyOrFail({$iterable} \$entities, {$optionsType} \$options = [])";
		}

		// Make replaceable via parsed object
		$result = [];
		foreach ($annotations as $annotation) {
			$annotationObject = AnnotationFactory::createFromString($annotation);
			if (!$annotationObject) {
				throw new RuntimeException('Cannot factorize annotation ' . $annotation);
			}

			$result[] = $annotationObject;
		}

		$result = $this->addBehaviorMixins($result, $behaviors);
		$result = $this->addBehaviorExtends($result, $behaviors, $parentClass);

		return $result;
	}

	/**
	 * @param string $entityClass
	 * @param string $entityName
	 * @param \Cake\Database\Schema\TableSchemaInterface $schema
	 * @param \Cake\ORM\AssociationCollection $associations
	 *
	 * @return bool
	 */
	protected function entity(string $entityClass, string $entityName, TableSchemaInterface $schema, AssociationCollection $associations): bool {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$entityPaths = AppPath::get('Model/Entity', $plugin);
		$entityPath = null;
		while ($entityPaths) {
			$pathTmp = array_shift($entityPaths);
			$pathTmp = str_replace('\\', DS, $pathTmp);
			if (file_exists($pathTmp . $entityName . '.php')) {
				$entityPath = $pathTmp . $entityName . '.php';

				break;
			}
		}
		if (!$entityPath) {
			return false;
		}

		$file = pathinfo($entityPath, PATHINFO_BASENAME);
		$this->_io->verbose('   ' . $file);

		$annotator = $this->getEntityAnnotator($entityClass, $schema, $associations);
		$annotator->annotate($entityPath);

		return true;
	}

	/**
	 * @param string $content
	 * @return array<string>
	 */
	protected function parseLoadedBehaviors(string $content): array {
		preg_match_all('/\$this->addBehavior\(\'([a-z.\/]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$behaviors = array_unique($matches[1]);

		$result = [];
		foreach ($behaviors as $behavior) {
			[, $behaviorName] = pluginSplit($behavior);
			$result[$behaviorName] = $behavior;
		}

		return $result;
	}

	/**
	 * @param \Cake\ORM\AssociationCollection $tableAssociations
	 * @return array<string, array<string, string>>
	 */
	protected function getAssociations(AssociationCollection $tableAssociations): array {
		$associations = [];
		foreach ($tableAssociations->keys() as $key) {
			$association = $tableAssociations->get($key);
			if (!$association) {
				continue;
			}
			$type = get_class($association);

			[, $name] = pluginSplit($association->getAlias());
			$table = $association->getClassName() ?: $association->getAlias();
			$className = App::className($table, 'Model/Table', 'Table') ?: static::CLASS_TABLE;

			$associations[$type][$name] = $className;
		}

		return $associations;
	}

	/**
	 * @param \Cake\ORM\Table $table
	 * @return array<string>
	 */
	protected function getBehaviors($table): array {
		$object = $table->behaviors();
		$map = $this->invokeProperty($object, '_loaded');

		$behaviors = $this->extractBehaviors($map);
		/** @phpstan-var class-string<object>|false $parentClass */
		$parentClass = get_parent_class($table);
		if (!$parentClass) {
			return [];
		}

		if (isset($this->_cache[$parentClass])) {
			$parentBehaviors = $this->_cache[$parentClass];
		} else {
			$parentReflection = new ReflectionClass($parentClass);
			if (!$parentReflection->isInstantiable()) {
				return $behaviors;
			}

			/** @var \Cake\ORM\Table $parent */
			$parent = new $parentClass();

			$object = $parent->behaviors();
			$map = $this->invokeProperty($object, '_loaded');
			$this->_cache[$parentClass] = $parentBehaviors = $this->extractBehaviors($map);
		}

		return array_diff_key($behaviors, $parentBehaviors);
	}

	/**
	 * @param array<string> $map
	 * @return array<string>
	 */
	protected function extractBehaviors(array $map): array {
		$result = [];
		/** @var object|string $behavior */
		foreach ($map as $name => $behavior) {
			$behaviorClassName = get_class($behavior) ?: '';
			$behaviorName = $this->resolveBehaviorName($behaviorClassName, $name);
			$pluginName = $this->resolvePluginName($behaviorClassName, $name);
			if ($pluginName) {
				$pluginName .= '.';
			}
			$result[$name] = $pluginName . $behaviorName;
		}

		return $result;
	}

	/**
	 * @param string $className
	 * @param string $name
	 * @return string|null
	 */
	protected function resolveBehaviorName(string $className, string $name): ?string {
		preg_match('#\\\\(?:Model|ORM)\\\\Behavior\\\\(.+)Behavior$#', $className, $matches);
		if (!$matches) {
			return null;
		}

		return str_replace('\\', '/', $matches[1]);
	}

	/**
	 * @param string $className
	 * @param string $name
	 *
	 * @return string|null
	 */
	protected function resolvePluginName(string $className, string $name): ?string {
		if (str_starts_with($className, 'Cake\\ORM')) {
			return '';
		}
		if (str_starts_with($className, 'App\\Model\\')) {
			return '';
		}

		if (str_contains($name, '\\')) {
			preg_match('#^(.+?)\\\\Model\\\\Behavior\\\\#', $className, $matches);
			if (!$matches) {
				return null;
			}
		} else {
			preg_match('#^(.+?)\\\\Model\\\\Behavior\\\\(.+)Behavior$#', $className, $matches);
			if (!$matches) {
				return null;
			}
		}

		return str_replace('\\', '/', $matches[1]);
	}

	/**
	 * @param string $entityClass
	 * @param \Cake\Database\Schema\TableSchemaInterface $schema
	 * @param \Cake\ORM\AssociationCollection $associations
	 * @return \IdeHelper\Annotator\AbstractAnnotator
	 */
	protected function getEntityAnnotator(string $entityClass, TableSchemaInterface $schema, AssociationCollection $associations): AbstractAnnotator {
		$class = EntityAnnotator::class;
		/** @phpstan-var array<class-string<\IdeHelper\Annotator\AbstractAnnotator>> $tasks */
		$tasks = (array)Configure::read('IdeHelper.annotators');
		if (isset($tasks[$class])) {
			$class = $tasks[$class];
		}

		return new $class($this->_io, ['class' => $entityClass, 'schema' => $schema, 'associations' => $associations] + $this->getConfig());
	}

	/**
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $result
	 * @param array<string> $behaviors
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function addBehaviorMixins(array $result, array $behaviors): array {
		if (!in_array(static::BEHAVIOR_MIXIN, $this->_config[static::TABLE_BEHAVIORS], true)) {
			return $result;
		}

		foreach ($behaviors as $behavior) {
			$className = App::className($behavior, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = App::className($behavior, 'ORM/Behavior', 'Behavior');
			}
			if (!$className) {
				continue;
			}

			$result[] = AnnotationFactory::createOrFail(MixinAnnotation::TAG, "\\{$className}");
		}

		return $result;
	}

	/**
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $result
	 * @param array<string> $behaviors
	 * @param string $parentClass
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function addBehaviorExtends(array $result, array $behaviors, string $parentClass): array {
		if (!in_array(static::BEHAVIOR_EXTENDS, $this->_config[static::TABLE_BEHAVIORS], true)) {
			return $result;
		}

		$list = [];
		foreach ($behaviors as $name => $fullName) {
			$className = App::className($fullName, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = App::className($fullName, 'ORM/Behavior', 'Behavior');
			}
			if (!$className) {
				continue;
			}

			$list[] = $name . ': \\' . $className;
		}

		if (!$list) {
			return $result;
		}

		sort($list);

		$list = implode(', ', $list);

		if (!$parentClass) {
			$parentClass = '\\Cake\\ORM\\Table';
		}
		$result[] = AnnotationFactory::createOrFail(ExtendsAnnotation::TAG, '\\' . $parentClass . '<array{' . $list . '}>');

		return $result;
	}

}
