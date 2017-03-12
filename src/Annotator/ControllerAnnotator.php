<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Utility\Inflector;
use Cake\View\View;
use IdeHelper\Console\Io;
use PHP_CodeSniffer_Tokens;

/**
 */
class ControllerAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if (substr($className, -13) === 'AppController' || substr($className, -10) !== 'Controller') {
			return null;
		}

		$content = file_get_contents($path);
		$primaryModelClass = $this->_getPrimaryModelClass($content, $className);

		$usedModels = $this->_getUsedModels($content);
		$usedModels[] = $primaryModelClass;
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		$helper = new DocBlockHelper(new View());

		$annotationString = $helper->classDescription('', '', $annotations);

		$file = $this->_getFile($path);
		$file->start($content);

		$tokens = $file->getTokens();

		$classIndex = $file->findNext(T_CLASS, 0);

		$prevCode = $file->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $classIndex, null, true);

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classIndex, $prevCode);
		if ($closeTagIndex) {
			return false;
		}

		$fixer = $this->_getFixer();
		$fixer->startFile($file);

		$docBlock = $annotationString . PHP_EOL;
		$fixer->replaceToken($classIndex, $docBlock . $tokens[$classIndex]['content']);

		$contents = $fixer->getContents();

		$this->_storeFile($path, $contents);

		$this->_io->out('   * ' . count($annotations) . ' annotations added');

		return true;
	}

	/**
	 * @param string $content
	 * @param $className
	 * @return null|string
	 */
	protected function _getPrimaryModelClass($content, $className) {
		if (preg_match('/\bpublic \$modelClass = \'([a-z.]+)\'/i', $content, $matches)) {
			return $matches[1];
		}

		if (preg_match('/\bpublic \$modelClass = false;/i', $content, $matches)) {
			return null;
		}

		$modelName = substr($className, 0, -10);
		if ($this->config(static::CONFIG_PLUGIN)) {
			$modelName = $this->config(static::CONFIG_PLUGIN) . $modelName;
		}

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
