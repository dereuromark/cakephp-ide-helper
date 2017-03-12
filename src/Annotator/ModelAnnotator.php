<?php
namespace IdeHelper\Annotator;

use Bake\Shell\Task\ModelTask;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use IdeHelper\Console\Io;

/**
 */
class ModelAnnotator extends AbstractAnnotator {

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array $config
	 */
	public function __construct(Io $io, array $config) {
		parent::__construct($io, $config);
	}

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

		$task = new ModelTask();
		$schema = $task->getEntityPropertySchema($table);
		if (!$schema) {
			return null;
		}

		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$table = TableRegistry::get($plugin ? ($plugin . '.' . $modelName) : $modelName);

		$entityClassName = $table->getEntityClass();
		$entityName = substr($entityClassName, strrpos($entityClassName, '\\') + 1);

		$resTable = $this->_table($path, $className, $entityName);
		$resEntity = $this->_entity($entityName, $schema);

		return $resTable || $resEntity;
	}

	/**
	 * @param string $path
	 * @param string $className
	 * @param string $entityName
	 *
	 * @return bool
	 */
	protected function _table($path, $className, $entityName) {
		$content = file_get_contents($path);
		if (preg_match('/\* @method .+ \$/', $content)) {
			return false;
		}

		$entity = $entityName;

		//TODO
		$associations = [];
		//TODO
		$behaviors = [];

		$namespace = $this->getConfig(static::CONFIG_NAMESPACE);
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $assoc) {
				$typeStr = Inflector::camelize($type);
				$annotations[] = "@property \\Cake\\ORM\\Association\\{$typeStr} \${$assoc['alias']}";
			}
		}
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} get(\$primaryKey, \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} newEntity(\$data = null, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] newEntities(array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}|bool save(\\Cake\\Datasource\\EntityInterface \$entity, \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} patchEntity(\\Cake\\Datasource\\EntityInterface \$entity, array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity}[] patchEntities(\$entities, array \$data, array \$options = [])";
		$annotations[] = "@method \\{$namespace}\\Model\\Entity\\{$entity} findOrCreate(\$search, callable \$callback = null, \$options = [])";
		foreach ($behaviors as $behavior => $behaviorData) {
			$annotations[] = "@mixin \\Cake\\ORM\\Behavior\\{$behavior}Behavior";
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

}
