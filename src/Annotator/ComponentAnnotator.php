<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
use PHP_CodeSniffer_Tokens;

/**
 */
class ComponentAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);
		$annotations = [];

		$name = pathinfo($path, PATHINFO_FILENAME);
		$name = substr($name, 0, -9);
		$className = App::className($name, 'Controller/Component', 'Component');
		$object = new $className(new ComponentRegistry());

		$helperMap = $this->invokeProperty($object, '_componentMap');

		$componentAnnotations = $this->_getComponentAnnotations($helperMap);
		foreach ($componentAnnotations as $helperAnnotation) {
			if (preg_match('/' . preg_quote($helperAnnotation) . '/', $content)) {
				continue;
			}

			$annotations[] = $helperAnnotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param array $annotations
	 *
	 * @return bool
	 */
	protected function _annotate($path, $content, array $annotations) {
		if (!$annotations) {
			return false;
		}

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
	 * @param array $map
	 * @return array
	 */
	protected function _getComponentAnnotations($map) {
		if (empty($map)) {
			return [];
		}

		$annotations = [];
		foreach ($map as $name => $config) {
			$className = $this->_findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$annotations[] = '@property \\' . $className . ' $' . $name;
		}

		return $annotations;
	}

	/**
	 * @param string $component
	 *
	 * @return string|null
	 */
	protected function _findClassName($component) {
		$plugins = Plugin::loaded();
		if (class_exists($component)) {
			return $component;
		}

		$className = App::className($component, 'Controller/Component', 'Component');
		if ($className) {
			return $className;
		}

		foreach ($plugins as $plugin) {
			$className = App::className($plugin . '.' . $component, 'Controller/Component', 'Component');
			if ($className) {
				return $className;
			}
		}

		return null;
	}

}
