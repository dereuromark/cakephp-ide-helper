<?php

namespace IdeHelper\Annotator;

use Cake\Core\Configure;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\AssociationCollection;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\GenericString;
use ReflectionClass;
use RuntimeException;
use Throwable;

class ModelAnnotator extends AbstractAnnotator {

	public const CLASS_TABLE = Table::class;

	/**
	 * @var array<string, array<string, string>>
	 */
	protected $_cache = [];

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Table' || substr($className, -5) !== 'Table') {
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

		$resTable = $this->table($path, $entityName, $associations, $behaviors);
		$resEntity = $this->entity($entityClassName, $entityName, $schema, $tableAssociations);

		return $resTable || $resEntity;
	}

	/**
	 * @param string $path
	 * @param string $entityName
	 * @param array<string, mixed> $associations
	 * @param array<string> $behaviors
	 *
	 * @return bool
	 */
	protected function table(string $path, string $entityName, array $associations, array $behaviors): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$behaviors += $this->parseLoadedBehaviors($content);
		$annotations = $this->buildAnnotations($associations, $entityName, $behaviors);

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param array<string, mixed> $associations
	 * @param string $entity
	 * @param array<string> $behaviors
	 *
	 * @throws \RuntimeException
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(array $associations, string $entity, array $behaviors): array {
		$namespace = $this->getConfig(static::CONFIG_NAMESPACE);
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $name => $className) {
				$annotations[] = "@property \\{$className}&\\{$type} \${$name}";
			}
		}

		$fullClassName = "$namespace\\Model\\Entity\\$entity";
		if (class_exists($fullClassName)) {
			$fullClassName = '\\' . $fullClassName;
			$fullClassNameCollection = GenericString::generate($fullClassName);

			/**
			 * Copied from Bake plugin's DocBlockHelper
			 *
			 * @link \Bake\View\Helper\DocBlockHelper::buildTableAnnotations()
			 */
			$annotations[] = "@method $fullClassName newEmptyEntity()";
			$annotations[] = "@method $fullClassName newEntity(array \$data, array \$options = [])";
			$annotations[] = "@method $fullClassNameCollection newEntities(array \$data, array \$options = [])";

			$annotations[] = "@method $fullClassName get(\$primaryKey, \$options = [])";
			$annotations[] = "@method $fullClassName findOrCreate(\$search, ?callable \$callback = null, \$options = [])";

			$entityInterface = '\\' . EntityInterface::class;

			$annotations[] = "@method $fullClassName patchEntity({$entityInterface} \$entity, array \$data, array \$options = [])";
			$annotations[] = "@method $fullClassNameCollection patchEntities(iterable \$entities, array \$data, array \$options = [])";

			$annotations[] = "@method $fullClassName|false save({$entityInterface} \$entity, \$options = [])";
			$annotations[] = "@method $fullClassName saveOrFail({$entityInterface} \$entity, \$options = [])";

			$resultSetInterfaceCollection = GenericString::generate($fullClassName, '\\' . ResultSetInterface::class);

			$annotations[] = "@method {$resultSetInterfaceCollection}|false saveMany(iterable \$entities, \$options = [])";
			$annotations[] = "@method {$resultSetInterfaceCollection} saveManyOrFail(iterable \$entities, \$options = [])";

			$annotations[] = "@method {$resultSetInterfaceCollection}|false deleteMany(iterable \$entities, \$options = [])";
			$annotations[] = "@method {$resultSetInterfaceCollection} deleteManyOrFail(iterable \$entities, \$options = [])";
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

			if ($type !== BelongsToMany::class) {
				continue;
			}

			/** @var \Cake\ORM\Association\BelongsToMany $association */
			$through = $this->throughAlias($association);
			if (!$through) {
				continue;
			}

			$className = App::className($through, 'Model/Table', 'Table') ?: static::CLASS_TABLE;
			[, $throughName] = pluginSplit($through);
			if (strpos($throughName, '\\') !== false) {
				$throughName = substr($throughName, strrpos($throughName, '\\') + 1, -5);
			}
			$type = HasMany::class;
			if (isset($associations[$type][$throughName])) {
				continue;
			}

			$associations[$type][$throughName] = $className;
		}

		return $associations;
	}

	/**
	 * @param \Cake\ORM\Association\BelongsToMany $association
	 * @return string
	 */
	protected function throughAlias(BelongsToMany $association): string {
		$through = $association->getThrough();
		if ($through) {
			if (is_object($through)) {
				return $through->getAlias();
			}

			return $through;
		}

		$tableName = $this->junctionTableName($association);
		$through = Inflector::camelize($tableName);

		return $through;
	}

	/**
	 * @uses \Cake\ORM\Association\BelongsToMany::_junctionTableName()
	 *
	 * @param \Cake\ORM\Association\BelongsToMany $association
	 * @return string
	 */
	protected function junctionTableName(BelongsToMany $association): string {
		$tablesNames = array_map('Cake\Utility\Inflector::underscore', [
			$association->getSource()->getTable(),
			$association->getTarget()->getTable(),
		]);

		sort($tablesNames);

		return implode('_', $tablesNames);
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
	protected function extractBehaviors(array $map) {
		$result = [];
		/** @var object|string $behavior */
		foreach ($map as $name => $behavior) {
			$behaviorClassName = get_class($behavior) ?: '';
			$pluginName = $this->resolvePluginName($behaviorClassName, $name);
			if ($pluginName === null) {
				continue;
			}
			if ($pluginName) {
				$pluginName .= '.';
			}
			$result[$name] = $pluginName . $name;
		}

		return $result;
	}

	/**
	 * @param string $className
	 * @param string $name
	 *
	 * @return string|null
	 */
	protected function resolvePluginName(string $className, string $name): ?string {
		if (strpos($className, 'Cake\\ORM') === 0) {
			return '';
		}
		if (strpos($className, 'App\\Model\\') === 0) {
			return '';
		}

		preg_match('#^(.+)\\\\Model\\\\Behavior\\\\' . $name . 'Behavior$#', $className, $matches);
		if (!$matches) {
			return null;
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

}
