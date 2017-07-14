<?php
namespace IdeHelper\Annotator;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Network\Request;
use Cake\Network\Session;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotator\Traits\ComponentTrait;

class ComponentAnnotator extends AbstractAnnotator {

	use ComponentTrait;

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$name = pathinfo($path, PATHINFO_FILENAME);
		if (substr($name, -9) !== 'Component') {
			return false;
		}

		$name = substr($name, 0, -9);
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'Controller/Component', 'Component');
		if (!$className) {
			return false;
		}

		$annotations = [];
		$content = file_get_contents($path);

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
	 * @param string $className
	 * @return array
	 */
	protected function _getComponentAnnotations($className) {
		$request = new Request();
		$request->session(new Session());
		$controller = new Controller();
		try {
			$object = new $className(new ComponentRegistry($controller));
		} catch (\Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping component annotations: ' . $e->getMessage());
			}
			return [];
		}

		$map = $this->_invokeProperty($object, '_componentMap');

		if (empty($map)) {
			return [];
		}

		$annotations = [];
		foreach ($map as $name => $config) {
			$className = $this->_findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$annotations[] = AnnotationFactory::create('@property', '\\' . $className, '$' . $name);
		}

		return $annotations;
	}

}
