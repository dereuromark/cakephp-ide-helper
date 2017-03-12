<?php
namespace IdeHelper\Annotator;

use Bake\Shell\Task\ModelTask;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOutput;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class ModelAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Table' || substr($className, -5) !== 'Table') {
			return null;
		}

		$modelName = substr($className, 0, -5);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$table = TableRegistry::get($plugin ? ($plugin . '.' . $modelName) : $modelName);

		$tmp = tempnam ('/tmp', 'annotator-');
		$task = new ModelTask(new ConsoleIo(new ConsoleOutput($tmp)));
		$schema = $task->getEntityPropertySchema($table);
		if (!$schema) {
			return null;
		}
		$task->connection = 'default';
		$associations = $task->getAssociations($table);

		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$table = TableRegistry::get($plugin ? ($plugin . '.' . $modelName) : $modelName);

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
			foreach ($assocs as $assoc) {
				//BC (https://github.com/cakephp/bake/pull/324)
				if (empty($assoc['namespace'])) {
					$tableName = !empty($assoc['className']) ? $assoc['className'] : $assoc['alias'];
					$assoc['namespace'] = App::className($tableName, 'Model/Table', 'Table');
				}
				if (empty($assoc['namespace'])) {
					continue;
				}

				$className = $assoc['namespace'];
				$typeStr = Inflector::camelize($type);
				$annotations[] = "@property \\{$className}|\\Cake\\ORM\\Association\\{$typeStr} \${$assoc['alias']}";
			}
		}
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} get(\$primaryKey, \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} newEntity(\$data = null, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] newEntities(array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}|bool save(\\Cake\\Datasource\\EntityInterface \$entity, \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} patchEntity(\\Cake\\Datasource\\EntityInterface \$entity, array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] patchEntities(\$entities, array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} findOrCreate(\$search, callable \$callback = null, \$options = [])";

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
			if (preg_match('/' . preg_quote($annotation) . '/', $content)) {
				unset($annotations[$key]);
			}
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $entityName
	 * @param array $schema
	 *
	 * @return bool|null
	 */
	protected function _entity($entityName, array $schema) {
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

}
