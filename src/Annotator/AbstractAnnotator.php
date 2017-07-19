<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\InstanceConfigTrait;
use Cake\View\View;
use IdeHelper\Annotation\AbstractAnnotation;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\ReplacableAnnotationInterface;
use IdeHelper\Console\Io;
use PHP_CodeSniffer;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer_File;
use ReflectionClass;
use SebastianBergmann\Diff\Differ;

$manualAutoload = getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}
if (!class_exists(Runner::class)) {
	class_alias('\PHP_CodeSniffer_File', File::class);
	class_alias('\PHP_CodeSniffer_Fixer', Fixer::class);
	class_alias('\PHP_CodeSniffer_Tokens', Tokens::class);
}

abstract class AbstractAnnotator {

	use InstanceConfigTrait;

	const CONFIG_DRY_RUN = 'dry-run';
	const CONFIG_PLUGIN = 'plugin';
	const CONFIG_NAMESPACE = 'namespace';
	const CONFIG_VERBOSE = 'verbose';

	/**
	 * @var bool
	 */
	public static $output = false;

	/**
	 * @var \IdeHelper\Console\Io
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
	 * @param string|null $content
	 *
	 * @return \PHP_CodeSniffer\Files\File
	 */
	protected function _getFile($file, $content = null) {
		$_SERVER['argv'] = [];

		if (!class_exists(Runner::class)) {
			// phpcs 2.x
			$phpcs = new PHP_CodeSniffer();
			$phpcs->process([], null, []);
			$fileObject = new PHP_CodeSniffer_File($file, [], [], $phpcs);

			$fileObject->start($content !== null ? $content : file_get_contents($file));

			return $fileObject;
		}

		$phpcs = new Runner();

		$config = new Config();
		$phpcs->config = $config;
		$phpcs->init();

		$ruleset = new Ruleset($config);

		$fileObject = new File($file, $ruleset, $config);
		$fileObject->setContent($content !== null ? $content : file_get_contents($file));
		$fileObject->parse();

		return $fileObject;
	}

	/**
	 * @param string $oldContent
	 * @param string $newContent
	 * @return void
	 */
	protected function _displayDiff($oldContent, $newContent) {
		$differ = new Differ(null);
		$array = $differ->diffToArray($oldContent, $newContent);

		$begin = null;
		$end = null;
		foreach ($array as $key => $row) {
			if ($row[1] === 0) {
				continue;
			}

			if ($begin === null) {
				$begin = $key;
			}
			$end = $key;
		}
		if ($begin === null) {
			return;
		}
		$firstLineOfOutput = $begin > 0 ? $begin - 1 : 0;
		$lastLineOfOutput = count($array) - 1 > $end ? $end + 1 : $end;

		for ($i = $firstLineOfOutput; $i <= $lastLineOfOutput; $i++) {
			$row = $array[$i];
			$char = ' ';
			if ($row[1] === 1) {
				$char = '+';
				$this->_io->info('   | ' . $char . $row[0], 1, Shell::VERBOSE);
			} elseif ($row[1] === 2) {
				$char = '-';
				$this->_io->out('<warning>' . '   | ' . $char . $row[0] . '</warning>', 1);
			} else {
				$this->_io->out('   | ' . $char . $row[0], 1, Shell::VERBOSE);
			}
		}
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
	 * @param \PHP_CodeSniffer\Files\File $file
	 *
	 * @return \PHP_CodeSniffer\Fixer
	 */
	protected function _getFixer($file) {
		$fixer = new Fixer();

		$fixer->startFile($file);

		return $fixer;
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param array $annotations
	 *
	 * @return bool
	 */
	protected function _annotate($path, $content, array $annotations) {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->_getFile($path);

		$classIndex = $file->findNext(T_CLASS, 0);

		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $classIndex - 1, null, true);

		$closeTagIndex = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classIndex - 1, $prevCode);
		if ($closeTagIndex) {
			$newContent = $this->_appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$newContent = $this->_addNewDocBlock($file, $classIndex, $annotations);
		}

		$this->_displayDiff($content, $newContent);
		$this->_storeFile($path, $newContent);

		if (count($annotations)) {
			$this->_io->success('   -> ' . count($annotations) . ' annotations added');
		} else {
			$this->_io->verbose('   -> ' . count($annotations) . ' annotations added');
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $closeTagIndex
	 * @param array &$annotations
	 *
	 * @return string
	 */
	protected function _appendToExistingDocBlock(File $file, $closeTagIndex, array &$annotations) {
		$existingAnnotations = $this->_parseExistingAnnotations($file, $closeTagIndex);

		/* @var \IdeHelper\Annotation\AbstractAnnotation[] $replacingAnnotations */
		$replacingAnnotations = [];
		$addingAnnotations = [];
		foreach ($annotations as $key => $annotation) {
			if (!is_object($annotation)) {
				$addingAnnotations[] = $annotation;
				continue;
			}
			if (!$this->_allowsReplacing($annotation, $existingAnnotations)) {
				unset($annotations[$key]);
				continue;
			}

			$toBeReplaced = $this->_needsReplacing($annotation, $existingAnnotations);
			if (!$toBeReplaced) {
				$addingAnnotations[] = $annotation;
				continue;
			}

			$replacingAnnotations[] = $toBeReplaced;
		}
		$tokens = $file->getTokens();

		$lastTagIndexOfPreviousLine = $closeTagIndex;
		while ($tokens[$lastTagIndexOfPreviousLine]['line'] === $tokens[$closeTagIndex]['line']) {
			$lastTagIndexOfPreviousLine--;
		}

		$needsNewline = $this->_needsNewLineInDocBlock($file, $lastTagIndexOfPreviousLine);

		$fixer = $this->_getFixer($file);

		$fixer->beginChangeset();

		foreach ($replacingAnnotations as $annotation) {
			$fixer->replaceToken($annotation->getIndex(), $annotation->build());
		}

		if (count($addingAnnotations)) {
			$annotationString = $needsNewline ? ' *' . "\n" : '';
			foreach ($addingAnnotations as $annotation) {
				$annotationString .= ' * ' . $annotation . "\n";
			}

			$fixer->addContent($lastTagIndexOfPreviousLine, $annotationString);
		}

		$fixer->endChangeset();

		$contents = $fixer->getContents();

		return $contents;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $existingAnnotations
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	protected function _needsReplacing(AbstractAnnotation $annotation, array $existingAnnotations) {
		foreach ($existingAnnotations as $existingAnnotation) {
			if ($existingAnnotation->matches($annotation)) {
				$newAnnotation = clone $existingAnnotation;
				$newAnnotation->replaceWith($annotation);

				return $newAnnotation;
			}
		}

		return null;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $existingAnnotations
	 * @return bool
	 */
	protected function _allowsReplacing(AbstractAnnotation $annotation, array $existingAnnotations) {
		foreach ($existingAnnotations as $existingAnnotation) {
			if (!$existingAnnotation instanceof ReplacableAnnotationInterface) {
				continue;
			}
			/* @var \IdeHelper\Annotation\ReplacableAnnotationInterface $existingAnnotation */
			if ($existingAnnotation->matches($annotation) && $existingAnnotation->getDescription() !== '') {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $closeTagIndex
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function _parseExistingAnnotations(File $file, $closeTagIndex) {
		$tokens = $file->getTokens();

		$startTagIndex = $tokens[$closeTagIndex]['comment_opener'];

		$annotations = [];
		for ($i = $startTagIndex + 1; $i < $closeTagIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@property', '@var', '@method'])) {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			$appendix = '';
			$spacePos = strpos($content, ' ');
			if ($spacePos) {
				$appendix = substr($content, $spacePos);
				$content = substr($content, 0, $spacePos);
			}

			$annotations[] = AnnotationFactory::create($tokens[$i]['content'], $content, trim($appendix), $classNameIndex);
		}

		return $annotations;
	}

	/**
 * @param \File $file
 * @param int $lastTagIndexOfPreviousLine
 *
 * @return bool
 */
	protected function _needsNewLineInDocBlock(File $file, $lastTagIndexOfPreviousLine) {
		$tokens = $file->getTokens();

		$line = $tokens[$lastTagIndexOfPreviousLine]['line'];
		$index = $lastTagIndexOfPreviousLine - 1;
		while ($tokens[$index]['line'] === $line) {
			if ($tokens[$index]['code'] === T_DOC_COMMENT_TAG || $tokens[$index]['code'] === T_DOC_COMMENT_OPEN_TAG) {
				return false;
			}
			$index--;
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param string $classIndex
	 * @param \IdeHelper\Annotation\AbstractAnnotation[]|string[] $annotations
	 *
	 * @return string
	 */
	protected function _addNewDocBlock(File $file, $classIndex, array $annotations) {
		$tokens = $file->getTokens();

		foreach ($annotations as $key => $annotation) {
			if (is_string($annotation)) {
				continue;
			}
			$annotations[$key] = (string)$annotation;
		}

		$helper = new DocBlockHelper(new View());
		$annotationString = $helper->classDescription('', '', (array)$annotations);

		$fixer = $this->_getFixer($file);

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
			$regexAnnotation = str_replace('\$', '[\$]?', preg_quote($annotation));
			if (preg_match('/' . $regexAnnotation . '/', $content)) {
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
	 * (assuming the property was directly publicly accessible)
	 *
	 * @param object &$object Instantiated object that we want the property off.
	 * @param string $name Property name to fetch.
	 *
	 * @return mixed Property value.
	 */
	protected function _invokeProperty(&$object, $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
	}

}
