<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\AssociationCollection;
use Cake\ORM\TableRegistry;
use Exception;
use IdeHelper\Annotation\AnnotationFactory;

class ModelAnnotator extends AbstractAnnotator {

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
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping table and entity: ' . $e->getMessage());
			}
			return false;
		}

		try {
			$associations = $this->_getAssociations($table->associations());
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping associations: ' . $e->getMessage());
			}
			$associations = [];
		}

		$entityClassName = $table->getEntityClass();
		$entityName = substr($entityClassName, strrpos($entityClassName, '\\') + 1);

		$resTable = $this->_table($path, $entityName, $associations);
		$resEntity = $this->_entity($entityName, $schema);

		return $resTable || $resEntity;
	}

	/**
	 * @param string $path
	 * @param string $entityName
	 * @param array $associations
	 *
	 * @return bool
	 */
	protected function _table($path, $entityName, array $associations) {
		$content = file_get_contents($path);

		$entity = $entityName;

		$behaviors = $this->_parseLoadedBehaviors($content);

		$namespace = $this->getConfig(static::CONFIG_NAMESPACE);
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $name => $className) {
				$annotations[] = "@property \\{$className}|\\{$type} \${$name}";
			}
		}
		if (class_exists("{$namespace}\\Model\\Entity\\{$entity}")) {
			// Copied from Bake plugin
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} get(\$primaryKey, \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} newEntity(\$data = null, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] newEntities(array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}|bool save(\\Cake\\Datasource\\EntityInterface \$entity, \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} patchEntity(\\Cake\\Datasource\\EntityInterface \$entity, array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] patchEntities(\$entities, array \$data, array \$options = [])";
			$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} findOrCreate(\$search, callable \$callback = null, \$options = [])";
		}
		// Make replacable via parsed object
		foreach ($annotations as $key => $annotation) {
			$annotation = AnnotationFactory::createFromString($annotation);
			if (!$annotation) {
				continue;
			}

			$annotations[$key] = $annotation;
		}

		foreach ($behaviors as $behavior) {
			$className = App::className($behavior, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = App::className($behavior, 'ORM/Behavior', 'Behavior');
			}
			if (!$className) {
				continue;
			}

			$annotations[] = "@mixin \\{$className}";
		}

		foreach ($annotations as $key => $annotation) {
			$regexAnnotation = str_replace('\$', '[\$]?', preg_quote($annotation));
			if (preg_match('/' . $regexAnnotation . '/', $content)) {
				unset($annotations[$key]);
			}
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $entityName
	 * @param \Cake\Database\Schema\TableSchema $schema
	 *
	 * @return bool|null
	 */
	protected function _entity($entityName, TableSchema $schema) {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$entityPaths = App::path('Model/Entity', $plugin);
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

		$annotator = new EntityAnnotator($this->_io, ['schema' => $schema] + $this->getConfig());
		$annotator->annotate($entityPath);

		return true;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	protected function _parseLoadedBehaviors($content) {
		preg_match_all('/\$this-\>addBehavior\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches)) {
			return [];
		}

		$behaviors = $matches[1];

		return array_unique($behaviors);
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

			$name = $association->getAlias();
			$table = $association->className() ?: $association->getAlias();
			$className = App::className($table, 'Model/Table', 'Table');
			if (!$className) {
				continue;
			}

			$associations[$type][$name] = $className;
		}
		return $associations;
	}

}
