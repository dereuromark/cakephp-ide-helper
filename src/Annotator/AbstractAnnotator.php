<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Core\InstanceConfigTrait;
use Cake\View\View;
use IdeHelper\Console\Io;
use PHP_CodeSniffer;
use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Fixer;
use PHP_CodeSniffer_Tokens;
use ReflectionClass;

/**
 */
abstract class AbstractAnnotator {

	use InstanceConfigTrait;

	const CONFIG_DRY_RUN = 'dry-run';
	const CONFIG_PLUGIN = 'plugin';
	const CONFIG_NAMESPACE = 'namespace';

	/**
	 * @var bool
	 */
	public static $output = false;

	/**
	 * @var \Cake\Console\ConsoleIo
	 */
	protected $_io;

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		self::CONFIG_PLUGIN => null,
	];

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array $config
	 */
	public function __construct(Io $io, array $config) {
		$this->_io = $io;
		$this->setConfig($config);

		$namespace = $this->getConfig(static::CONFIG_PLUGIN) ?: 'App';
		$namespace = str_replace('/', '\\', $namespace);
		$this->setConfig(static::CONFIG_NAMESPACE, $namespace);
	}

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	abstract public function annotate($path);

	/**
	 * @param string $file
	 *
	 * @return \PHP_CodeSniffer_File
	 */
	protected function _getFile($file) {
		$_SERVER['argv'] = [];

		$phpcs = new PHP_CodeSniffer();
		$phpcs->process([], null, []);
		return new PHP_CodeSniffer_File($file, [], [], $phpcs);
	}

	/**
	 * @param string $path
	 * @param string $contents
	 * @return void
	 */
	protected function _storeFile($path, $contents) {
		static::$output = true;

		if ($this->getConfig(static::CONFIG_DRY_RUN)) {
			return;
		}
		file_put_contents($path, $contents);
	}

	/**
	 * @return \PHP_CodeSniffer_Fixer
	 */
	protected function _getFixer() {
		return new PHP_CodeSniffer_Fixer();
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

		$file = $this->_getFile($path);
		$file->start($content);

		$classIndex = $file->findNext(T_CLASS, 0);

		$prevCode = $file->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $classIndex - 1, null, true);

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classIndex - 1, $prevCode);
		if ($closeTagIndex) {
			$contents = $this->_appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$contents = $this->_addNewDocBlock($file, $classIndex, $annotations);
		}

		$this->_storeFile($path, $contents);

		if (count($annotations)) {
			$this->_io->out('   * ' . count($annotations) . ' annotations added');
		} else {
			$this->_io->verbose('   * ' . count($annotations) . ' annotations added');
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer_File $file
	 * @param int $closeTagIndex
	 * @param array $annotations
	 *
	 * @return string
	 */
	protected function _appendToExistingDocBlock(PHP_CodeSniffer_File $file, $closeTagIndex, $annotations) {
		$tokens = $file->getTokens();

		$lastTagIndexOfPreviousLine = $closeTagIndex;
		while ($tokens[$lastTagIndexOfPreviousLine]['line'] === $tokens[$closeTagIndex]['line']) {
			$lastTagIndexOfPreviousLine--;
		}

		$needsNewline = $this->_needsNewLineInDocBlock($file, $lastTagIndexOfPreviousLine);

		$fixer = $this->_getFixer();
		$fixer->startFile($file);

		$annotationString = $needsNewline ? ' *' . "\n" : '';
		foreach ($annotations as $annotation) {
			$annotationString .= ' * ' . $annotation . "\n";
		}

		$fixer->addContent($lastTagIndexOfPreviousLine, $annotationString);

		$contents = $fixer->getContents();

		return $contents;
	}

	/**
	 * @param \PHP_CodeSniffer_File $file
	 * @param int $lastTagIndexOfPreviousLine
	 *
	 * @return bool
	 */
	protected function _needsNewLineInDocBlock(PHP_CodeSniffer_File $file, $lastTagIndexOfPreviousLine) {
		$tokens = $file->getTokens();

		$line = $tokens[$lastTagIndexOfPreviousLine]['line'];
		$index = $lastTagIndexOfPreviousLine - 1;
		while ($tokens[$index]['line'] === $line) {
			if ($tokens[$index]['code'] === T_DOC_COMMENT_TAG) {
				return false;
			}
			$index--;
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer_File $file
	 * @param string $classIndex
	 * @param array $annotations
	 *
	 * @return string
	 */
	protected function _addNewDocBlock(PHP_CodeSniffer_File $file, $classIndex, array $annotations) {
		$tokens = $file->getTokens();

		$helper = new DocBlockHelper(new View());
		$annotationString = $helper->classDescription('', '', $annotations);

		$fixer = $this->_getFixer();
		$fixer->startFile($file);

		$docBlock = $annotationString . PHP_EOL;
		$fixer->replaceToken($classIndex, $docBlock . $tokens[$classIndex]['content']);

		$contents = $fixer->getContents();

		return $contents;
	}

	/**
	 * @param array $usedModels
	 * @param string $content
	 * @return array
	 */
	protected function _getModelAnnotations($usedModels, $content) {
		$annotations = [];

		foreach ($usedModels as $usedModel) {
			$className = App::className($usedModel, 'Model/Table', 'Table');
			if (!$className) {
				continue;
			}
			list(, $name) = pluginSplit($usedModel);

			$annotation = '@property \\' . $className . ' $' . $name;
			if (preg_match('/' . preg_quote($annotation) . '/', $content)) {
				continue;
			}

			$annotations[] = $annotation;
		}

		return $annotations;
	}

	/**
	 * Gets protected/private property of a class.
	 *
	 * So
	 *   $this->invokeProperty($object, '_foo');
	 * is equal to
	 *   $object->_foo
	 * (assuming the property was directly publicly accessible
	 *
	 * @param object &$object Instantiated object that we want the property off.
	 * @param string $name Property name to fetch.
	 *
	 * @return mixed Property value.
	 */
	protected function invokeProperty(&$object, $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
	}

}
