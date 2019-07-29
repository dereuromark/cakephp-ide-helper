<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\AssociationCollection;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Exception;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Utility\AppPath;
use RuntimeException;
use Throwable;

class ModelAnnotator extends AbstractAnnotator {

	const CLASS_TABLE = Table::class;

	/**
	 * @var array
	 */
	protected $_cache = [];

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Table' || substr($className, -5) !== 'Table') {
			return false;
		}

		$modelName = substr($className, 0, -5);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);

		try {
			$table = TableRegistry::get($plugin ? ($plugin . '.' . $modelName) : $modelName);
			$schema = $table->getSchema();
			$behaviors = $this->_getBehaviors($table);
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: ' . $e->getMessage());
			}
			return false;
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: ' . $e->getMessage());
			}
			return false;
		}

		$tableAssociations = [];
		try {
			$tableAssociations = $table->associations();
			$associations = $this->_getAssociations($tableAssociations);
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping associations: ' . $e->getMessage());
			}
			$associations = [];
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping associations: ' . $e->getMessage());
			}
			$associations = [];
		}

		$entityClassName = $table->getEntityClass();
		$entityName = substr($entityClassName, strrpos($entityClassName, '\\') + 1);

		$resTable = $this->_table($path, $entityName, $associations, $behaviors);
		$resEntity = $this->_entity($entityClassName, $entityName, $schema, $tableAssociations);

		return $resTable || $resEntity;
	}

	/**
	 * @param string $path
	 * @param string $entityName
	 * @param array $associations
	 * @param array $behaviors
	 *
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function _table($path, $entityName, array $associations, array $behaviors) {
		$content = file_get_contents($path);

		$entity = $entityName;

		$behaviors += $this->_parseLoadedBehaviors($content);

		$namespace = $this->getConfig(static::CONFIG_NAMESPACE);
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $name => $className) {
				$annotations[] = "@property \\{$className}&\\{$type} \${$name}";
			}
		}

		if (class_exists("{$namespace}\\Model\\Entity\\{$entity}")) {
			// Copied from Bake plugin's DocBlockHelper
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} get(\$primaryKey, \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} newEntity(\$data = null, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] newEntities(array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}|false save(\\Cake\\Datasource\\EntityInterface \$entity, \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} saveOrFail(\\Cake\\Datasource\\EntityInterface \$entity, \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} patchEntity(\\Cake\\Datasource\\EntityInterface \$entity, array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] patchEntities(\$entities, array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} findOrCreate(\$search, callable \$callback = null, \$options = [])";
		}
		// Make replacable via parsed object
		foreach ($annotations as $key => $annotation) {
			$annotationObject = AnnotationFactory::createFromString($annotation);
			if (!$annotationObject) {
				throw new RuntimeException('Cannot factorize annotation ' . $annotation);
			}

			$annotations[$key] = $annotationObject;
		}

		foreach ($behaviors as $behavior) {
			$className = App::className($behavior, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = App::className($behavior, 'ORM/Behavior', 'Behavior');
			}
			if (!$className) {
				continue;
			}

			$annotations[] = AnnotationFactory::createOrFail(MixinAnnotation::TAG, "\\{$className}");
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $entityClass
	 * @param string $entityName
	 * @param \Cake\Database\Schema\TableSchema $schema
	 * @param \Cake\ORM\AssociationCollection $associations
	 *
	 * @return bool|null
	 */
	protected function _entity($entityClass, $entityName, TableSchema $schema, AssociationCollection $associations) {
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
			return null;
		}

		$file = pathinfo($entityPath, PATHINFO_BASENAME);
		$this->_io->verbose('   ' . $file);

		$annotator = $this->getEntityAnnotator($entityClass, $schema, $associations);
		$annotator->annotate($entityPath);

		return true;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	protected function _parseLoadedBehaviors($content) {
		preg_match_all('/\$this-\>addBehavior\(\'([a-z.\/]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$behaviors = array_unique($matches[1]);

		$result = [];
		foreach ($behaviors as $behavior) {
			list (, $behaviorName) = pluginSplit($behavior);
			$result[$behaviorName] = $behavior;
		}

		return $result;
	}

	/**
	 * @param \Cake\ORM\AssociationCollection $tableAssociations
	 * @return array
	 */
	protected function _getAssociations(AssociationCollection $tableAssociations) {
		$associations = [];
		foreach ($tableAssociations->keys() as $key) {
			$association = $tableAssociations->get($key);
			$type = get_class($association);

			list(, $name) = pluginSplit($association->getAlias());
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
			list(, $throughName) = pluginSplit($through);
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
	protected function throughAlias(BelongsToMany $association) {
		$through = $association->getThrough();
		if ($through) {
			if (is_object($through)) {
				return $through->getAlias();
			}

			return $through;
		}

		$tableName = $this->_junctionTableName($association);
		$through = Inflector::camelize($tableName);

		return $through;
	}

	/**
	 * @uses \Cake\ORM\Association\BelongsToMany::_junctionTableName()
	 *
	 * @param \Cake\ORM\Association\BelongsToMany $association
	 * @return string
	 */
	protected function _junctionTableName(BelongsToMany $association) {
		$tablesNames = array_map('Cake\Utility\Inflector::underscore', [
			$association->getSource()->getTable(),
			$association->getTarget()->getTable()
		]);

		sort($tablesNames);

		return implode('_', $tablesNames);
	}

	/**
	 * @param \Cake\ORM\Table $table
	 * @return array
	 */
	protected function _getBehaviors($table) {
		$object = $table->behaviors();
		$map = $this->_invokeProperty($object, '_loaded');

		$behaviors = $this->_extractBehaviors($map);

		$parentClass = get_parent_class($table);
		if (isset($this->_cache[$parentClass])) {
			$parentBehaviors = $this->_cache[$parentClass];
		} else {
			/** @var \Cake\ORM\Table $parent */
			$parent = new $parentClass();

			$object = $parent->behaviors();
			$map = $this->_invokeProperty($object, '_loaded');
			$this->_cache[$parentClass] = $parentBehaviors = $this->_extractBehaviors($map);
		}

		$result = array_diff_key($behaviors, $parentBehaviors);

		return $result;
	}

	/**
	 * @param array $map
	 *
	 * @return array
	 */
	protected function _extractBehaviors(array $map) {
		$result = [];
		foreach ($map as $name => $behavior) {
			$behaviorClassName = get_class($behavior);
			$pluginName = $this->_resolvePluginName($behaviorClassName, $name);
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
	protected function _resolvePluginName($className, $name) {
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
	 * @param \Cake\Database\Schema\TableSchema $schema
	 * @param \Cake\ORM\AssociationCollection $associations
	 * @return \IdeHelper\Annotator\AbstractAnnotator
	 */
	protected function getEntityAnnotator($entityClass, TableSchema $schema, AssociationCollection $associations) {
		$class = EntityAnnotator::class;
		$tasks = (array)Configure::read('IdeHelper.annotators');
		if (isset($tasks[$class])) {
			$class = $tasks[$class];
		}

		return new $class($this->_io, ['class' => $entityClass, 'schema' => $schema, 'associations' => $associations] + $this->getConfig());
	}

}
