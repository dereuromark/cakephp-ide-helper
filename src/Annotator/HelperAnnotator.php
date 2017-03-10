<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
use IdeHelper\Console\Io;
use PHP_CodeSniffer_Tokens;
use ReflectionClass;

/**
 */
class HelperAnnotator extends AbstractAnnotator {

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
		$content = file_get_contents($path);
		$annotations = [];

		$name = pathinfo($path, PATHINFO_FILENAME);
		$name = substr($name, 0, -6);
		$className = App::className($name, 'View/Helper', 'Helper');
		$helper = new $className(new View());

		$helperMap = $this->invokeProperty($helper, '_helperMap');

		$helperAnnotations = $this->_getHelperAnnotations($helperMap);
		foreach ($helperAnnotations as $helperAnnotation) {
			if (preg_match('/' . preg_quote($helperAnnotation) . '/', $content)) {
				continue;
			}

			$annotations[] = $helperAnnotation;
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * Gets protected/private property of a class.
	 *
	 * So
	 *   $this->invokeMethod($user, 'cryptPassword', array('passwordToCrypt'));
	 * is equal to
	 *   $user->cryptPassword('passwordToCrypt');
	 * (assuming the method was directly publicly accessible
	 *
	 * @param object &$object Instantiated object that we will run method on.
	 * @param string $name Method name to call.
	 *
	 * @return mixed Method return.
	 */
	protected function invokeProperty(&$object, $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
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
	 * @param array $helperMap
	 * @return array
	 */
	protected function _getHelperAnnotations($helperMap) {
		if (empty($helperMap)) {
			return [];
		}

		$helperAnnotations = [];
		foreach ($helperMap as $helper => $config) {
			$className = $this->_findClassName($config['class']);
			if (!$className) {
				continue;
			}

			$helperAnnotations[] = '@property \\' . $className . ' $' . $helper;
		}

		return $helperAnnotations;
	}

	/**
	 * @param string $helper
	 *
	 * @return string|null
	 */
	protected function _findClassName($helper) {
		$plugins = Plugin::loaded();
		if (class_exists($helper)) {
			return $helper;
		}

		$className = App::className($helper, 'View/Helper', 'Helper');
		if ($className) {
			return $className;
		}

		foreach ($plugins as $plugin) {
			$className = App::className($plugin . '.' . $helper, 'View/Helper', 'Helper');
			if ($className) {
				return $className;
			}
		}

		return null;
	}

}
