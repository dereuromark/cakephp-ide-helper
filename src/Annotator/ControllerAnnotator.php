<?php
namespace IdeHelper\Annotator;

use Cake\Core\App;
use Cake\Network\Request;
use Cake\Network\Session;
use Exception;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotator\Traits\ComponentTrait;

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
		$primaryModelClass = $this->_getPrimaryModelClass($content, $className);

		$usedModels = $this->_getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		$componentAnnotations = $this->_getComponentAnnotations($className);
		foreach ($componentAnnotations as $componentAnnotation) {
			$regexAnnotation = str_replace('\$', '[\$]?', preg_quote($componentAnnotation));
			if (preg_match('/' . $regexAnnotation . '/', $content)) {
				continue;
			}

			$annotations[] = $componentAnnotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 * @param string $className
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
		if ($this->getConfig(static::CONFIG_PLUGIN)) {
			$modelName = $this->getConfig(static::CONFIG_PLUGIN) . '.' . $modelName;
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
	 * @param string $controllerName
	 * @return array
	 */
	protected function _getComponentAnnotations($controllerName) {
		try {
			$map = $this->_getUsedComponents($controllerName);
		} catch (Exception $e) {
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

			$annotations[] = AnnotationFactory::create('@property', '\\' . $className, '$' . $component);
		}

		return $annotations;
	}

	/**
	 * @param string $controllerName
	 *
	 * @return array
	 */
	protected function _getUsedComponents($controllerName) {
		$plugin = $controllerName !== 'AppController' ? $this->getConfig(static::CONFIG_PLUGIN) : null;
		$className = App::className(($plugin ? $plugin . '.' : '') . $controllerName, 'Controller');
		if (!$className) {
			return [];
		}

		$request = new Request();
		$request->session(new Session());
		/* @var \App\Controller\AppController $controller */
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

}
