<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Utility\Inflector;
use IdeHelper\Console\Io;

/**
 */
class ShellAnnotator extends AbstractAnnotator {

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
		if ($className === 'Shell' || substr($className, -5) !== 'Shell' && substr($className, -4) !== 'Task') {
			return null;
		}

		//$isTask = substr($className, -4) === 'Task';
		//$name = substr($className, 0, $isTask ? -4 : -5);

		$content = file_get_contents($path);
		$primaryModelClass = $this->_getPrimaryModelClass($content);
		$usedModels = $this->_getUsedModels($content);
		$usedModels[] = $primaryModelClass;
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function _getPrimaryModelClass($content) {
		if (!preg_match('/\bpublic \$modelClass = \'([a-z.]+)\'/i', $content, $matches)) {
			return null;
		}

		$modelName = $matches[1];

		return $modelName;
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _getUsedModels($content) {
		preg_match_all('/\$this-\>loadModel\(\'([a-z.]+)\'/i', $content, $matches);
		if (empty($matches)) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

	/**
	 * @param string $path
	 * @param string $className
	 * @param string $modelName
	 * @param string $entityName
	 *
	 * @return bool
	 */
	protected function _table($path, $className, $modelName, $entityName) {
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

		return true;
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

		$annotator = new EntityAnnotator($this->_io, ['schema' => $schema] + $this->_config);
		$annotator->annotate($entityPath);

		return true;
	}

}
