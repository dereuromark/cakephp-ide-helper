<?php

namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\View\View;
use IdeHelper\Annotation\AbstractAnnotation;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\MethodAnnotation;
use IdeHelper\Annotation\MixinAnnotation;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Annotation\PropertyReadAnnotation;
use IdeHelper\Annotation\UsesAnnotation;
use IdeHelper\Annotation\VariableAnnotation;
use IdeHelper\Annotator\Traits\DocBlockTrait;
use IdeHelper\Annotator\Traits\FileTrait;
use IdeHelper\Console\Io;
use IdeHelper\Utility\App;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use ReflectionClass;
use RuntimeException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

$composerVendorDir = getcwd() . DS . 'vendor';
$codesnifferDir = 'squizlabs' . DS . 'php_codesniffer';
if (!is_dir($composerVendorDir . DS . $codesnifferDir)) {
	$ideHelperDir = substr(__DIR__, 0, strpos(__DIR__, DS . 'cakephp-ide-helper') ?: 0);
	$composerVendorDir = dirname($ideHelperDir);
}
$manualAutoload = $composerVendorDir . DS . $codesnifferDir . DS . 'autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

abstract class AbstractAnnotator {

	use FileTrait;
	use InstanceConfigTrait;
	use DocBlockTrait;

	/**
	 * @var string
	 */
	public const CONFIG_DRY_RUN = 'dry-run';

	/**
	 * @var string
	 */
	public const CONFIG_PLUGIN = 'plugin';

	/**
	 * @var string
	 */
	public const CONFIG_NAMESPACE = 'namespace';

	/**
	 * @var string
	 */
	public const CONFIG_VERBOSE = 'verbose';

	/**
	 * @var string
	 */
	public const CONFIG_REMOVE = 'remove';

	/**
	 * @var string
	 */
	public const COUNT_REMOVED = 'removed';

	/**
	 * @var string
	 */
	public const COUNT_UPDATED = 'updated';

	/**
	 * @var string
	 */
	public const COUNT_ADDED = 'added';

	/**
	 * @var string
	 */
	public const COUNT_SKIPPED = 'skipped';

	/**
	 * @var array<string>
	 */
	public const TYPES = [
		PropertyAnnotation::TAG,
		PropertyReadAnnotation::TAG,
		VariableAnnotation::TAG,
		MethodAnnotation::TAG,
		MixinAnnotation::TAG,
		UsesAnnotation::TAG,
	];

	/**
	 * @var bool
	 */
	public static $output = false;

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $_io;

	/**
	 * @var array<string, mixed>
	 */
	protected $_defaultConfig = [
		self::CONFIG_PLUGIN => null,
	];

	/**
	 * @param \IdeHelper\Console\Io $io
	 * @param array<string, mixed> $config
	 */
	public function __construct(Io $io, array $config) {
		static::$output = false;
		$this->_io = $io;
		$this->setConfig($config);

		$namespace = $this->getConfig(static::CONFIG_PLUGIN) ?: Configure::read('App.namespace', 'App');
		$namespace = str_replace('/', '\\', $namespace);
		$this->setConfig(static::CONFIG_NAMESPACE, $namespace);
	}

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	abstract public function annotate(string $path): bool;

	/**
	 * @param string $oldContent
	 * @param string $newContent
	 * @return void
	 */
	protected function displayDiff(string $oldContent, string $newContent): void {
		$differ = new Differ(new DiffOnlyOutputBuilder());
		$array = $differ->diffToArray($oldContent, $newContent);

		$begin = null;
		$end = null;
		/**
		 * @var int $key
		 */
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
			$output = trim($row[0], "\n\r\0\x0B");

			if ($row[1] === 1) {
				$char = '+';
				$this->_io->info('   | ' . $char . $output, 1, Shell::VERBOSE);
			} elseif ($row[1] === 2) {
				$char = '-';
				$this->_io->out('<warning>' . '   | ' . $char . $output . '</warning>', 1, Shell::VERBOSE);
			} else {
				$this->_io->out('   | ' . $char . $output, 1, Shell::VERBOSE);
			}
		}
	}

	/**
	 * @param string $path
	 * @param string $contents
	 * @return void
	 */
	protected function storeFile(string $path, string $contents): void {
		static::$output = true;

		if ($this->getConfig(static::CONFIG_DRY_RUN)) {
			return;
		}
		file_put_contents($path, $contents);
	}

	/**
	 * @var array<string, int>
	 */
	protected $_counter = [];

	/**
	 * @param string $path
	 * @param string $content
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 *
	 * @return bool
	 */
	protected function annotateContent(string $path, string $content, array $annotations): bool {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->getFile($path);

		$classOrTraitIndex = $file->findNext([T_CLASS, T_TRAIT], 0);
		if (!$classOrTraitIndex) {
			return false;
		}
		$beginningOfLineIndex = $this->beginningOfLine($file, $classOrTraitIndex);

		$closeTagIndex = $this->findDocBlockCloseTagIndex($file, $beginningOfLineIndex);
		$this->resetCounter();
		if ($closeTagIndex && $this->shouldSkip($file, $closeTagIndex)) {
			return false;
		}

		if ($closeTagIndex && !$this->isInlineDocBlock($file, $closeTagIndex)) {
			$newContent = $this->appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$newContent = $this->addNewDocBlock($file, $beginningOfLineIndex, $annotations);
		}

		if ($newContent === $content) {
			$this->reportSkipped();

			return false;
		}

		$this->displayDiff($content, $newContent);
		$this->storeFile($path, $newContent);

		$this->report();

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $index First functional code after docblock
	 *
	 * @return int|null
	 */
	protected function findDocBlockCloseTagIndex(File $file, int $index): ?int {
		$prevCode = $file->findPrevious(Tokens::$emptyTokens, $index - 1, null, true);
		if (!$prevCode) {
			return null;
		}

		return $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $index - 1, $prevCode) ?: null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $docBlockCloseIndex
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 *
	 * @throws \RuntimeException
	 *
	 * @return string
	 */
	protected function appendToExistingDocBlock(File $file, int $docBlockCloseIndex, array &$annotations): string {
		$existingAnnotations = $this->parseExistingAnnotations($file, $docBlockCloseIndex);

		$replacingAnnotations = [];
		$addingAnnotations = [];
		foreach ($annotations as $key => $annotation) {
			if (!is_object($annotation)) {
				throw new RuntimeException('Must be object: ' . print_r($annotation, true));
			}
			if ($this->exists($annotation, $existingAnnotations)) {
				unset($annotations[$key]);

				continue;
			}

			if (!$this->allowsReplacing($annotation, $existingAnnotations)) {
				unset($annotations[$key]);
				$this->_counter[static::COUNT_SKIPPED]++;

				continue;
			}

			$toBeReplaced = $this->needsReplacing($annotation, $existingAnnotations);
			if (!$toBeReplaced) {
				$addingAnnotations[] = $annotation;

				continue;
			}

			$replacingAnnotations[] = $toBeReplaced;
		}

		$tokens = $file->getTokens();
		$lastTagIndexOfPreviousLine = $docBlockCloseIndex;
		while ($tokens[$lastTagIndexOfPreviousLine]['line'] === $tokens[$docBlockCloseIndex]['line']) {
			$lastTagIndexOfPreviousLine--;
		}

		$needsNewline = $this->needsNewLineInDocBlock($file, $lastTagIndexOfPreviousLine);

		$fixer = $this->getFixer($file);

		$fixer->beginChangeset();

		foreach ($replacingAnnotations as $annotation) {
			$fixer->replaceToken($annotation->getIndex(), $annotation->build());
			$this->_counter[static::COUNT_UPDATED]++;
		}

		if (count($addingAnnotations)) {
			$annotationString = $needsNewline ? ' *' . "\n" : '';
			foreach ($addingAnnotations as $annotation) {
				$annotationString .= ' * ' . $annotation . "\n";
				$this->_counter[static::COUNT_ADDED]++;
			}

			$fixer->addContent($lastTagIndexOfPreviousLine, $annotationString);
		}

		if ($this->getConfig(static::CONFIG_REMOVE)) {
			foreach ($existingAnnotations as $key => $existingAnnotation) {
				if ($existingAnnotation->isInUse()) {
					unset($existingAnnotations[$key]);

					continue;
				}

				if ($existingAnnotation->getDescription() !== '') {
					$this->_counter[static::COUNT_SKIPPED]++;
					unset($existingAnnotations[$key]);
				}
			}

			$removingAnnotations = $existingAnnotations;
			foreach ($removingAnnotations as $annotation) {
				$lastWhitespaceOfPreviousLine = $this->getLastWhitespaceOfPreviousLine($tokens, $annotation->getIndex());
				$index = $annotation->getIndex();
				for ($i = $lastWhitespaceOfPreviousLine; $i <= $index; $i++) {
					$fixer->replaceToken($i, '');
				}
				$this->_counter[static::COUNT_REMOVED]++;
			}
		}

		$fixer->endChangeset();

		return $fixer->getContents();
	}

	/**
	 * @param array<array<string, mixed>> $tokens
	 * @param int $index
	 *
	 * @return int
	 */
	protected function getLastWhitespaceOfPreviousLine(array $tokens, int $index): int {
		$currentLine = $tokens[$index]['line'];
		$index--;
		while ($tokens[$index]['line'] === $currentLine) {
			$index--;
		}

		return $index;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $existingAnnotations
	 * @return bool
	 */
	protected function exists(AbstractAnnotation $annotation, array &$existingAnnotations): bool {
		foreach ($existingAnnotations as $key => $existingAnnotation) {
			if ($existingAnnotation->build() === $annotation->build()) {
				unset($existingAnnotations[$key]);

				return true;
			}

			if ($annotation instanceof PropertyAnnotation && $existingAnnotation instanceof PropertyAnnotation) {
				if ($annotation->getProperty() === $existingAnnotation->getProperty() && $annotation->getType() === $existingAnnotation->getType()) {
					unset($existingAnnotations[$key]);

					return true;
				}
			}

			// Lets skip on existing ones that are only guessed.
			if ($annotation instanceof VariableAnnotation && $existingAnnotation instanceof VariableAnnotation) {
				if ($annotation->getVariable() === $existingAnnotation->getVariable() && $annotation->getGuessed()) {
					unset($existingAnnotations[$key]);

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $existingAnnotations
	 * @return \IdeHelper\Annotation\AbstractAnnotation|null
	 */
	protected function needsReplacing(AbstractAnnotation $annotation, array &$existingAnnotations) {
		foreach ($existingAnnotations as $key => $existingAnnotation) {
			if ($existingAnnotation->matches($annotation)) {
				$newAnnotation = clone $existingAnnotation;
				$newAnnotation->replaceWith($annotation);

				unset($existingAnnotations[$key]);

				return $newAnnotation;
			}
		}

		return null;
	}

	/**
	 * @param \IdeHelper\Annotation\AbstractAnnotation $annotation
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $existingAnnotations
	 * @return bool
	 */
	protected function allowsReplacing(AbstractAnnotation $annotation, array &$existingAnnotations): bool {
		foreach ($existingAnnotations as $key => $existingAnnotation) {
			if ($existingAnnotation->matches($annotation) && $existingAnnotation->getDescription() !== '') {
				unset($existingAnnotations[$key]);

				return false;
			}
		}

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $closeTagIndex
	 * @param array<string> $types
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function parseExistingAnnotations(File $file, int $closeTagIndex, array $types = self::TYPES): array {
		$tokens = $file->getTokens();

		/** @var int $startTagIndex */
		$startTagIndex = $tokens[$closeTagIndex]['comment_opener'];

		$annotations = [];
		for ($i = $startTagIndex + 1; $i < $closeTagIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], $types, true)) {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode|\PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode $valueNode */
			$valueNode = static::getValueNode($tokens[$i]['content'], $content);
			if ($valueNode instanceof InvalidTagValueNode) {
				continue;
			}

			$returnTypes = $this->valueNodeParts($valueNode);
			$typeString = $this->renderUnionTypes($returnTypes);

			$tag = $tokens[$i]['content'];
			$content = mb_substr($content, mb_strlen($typeString) + 1);

			$annotation = AnnotationFactory::createOrFail($tag, $typeString, $content, $classNameIndex);
			if ($this->getConfig(static::CONFIG_REMOVE) && $tag === VariableAnnotation::TAG && $this->varInUse($tokens, $closeTagIndex, $content)) {
				$annotation->setInUse();
			}
			if ($this->getConfig(static::CONFIG_REMOVE) && $tag === PropertyAnnotation::TAG && $this->propertyInUse($tokens, $closeTagIndex, $content)) {
				$annotation->setInUse();
			}
			if ($this->getConfig(static::CONFIG_REMOVE) && $tag === PropertyReadAnnotation::TAG && $this->propertyInUse($tokens, $closeTagIndex, $content)) {
				$annotation->setInUse();
			}
			if ($this->getConfig(static::CONFIG_REMOVE) && $tag === MethodAnnotation::TAG && $this->methodInUse($tokens, $closeTagIndex, $content)) {
				$annotation->setInUse();
			}

			$annotations[] = $annotation;
		}

		return $annotations;
	}

	/**
	 * Checks the var token for existence.
	 *
	 * T_VARIABLE ..., content=`$variable`
	 *
	 * @param array<array<string, mixed>> $tokens
	 * @param int $closeTagIndex
	 * @param string $variable
	 *
	 * @return bool
	 */
	protected function varInUse(array $tokens, int $closeTagIndex, string $variable): bool {
		if ($variable === '$this') {
			return false;
		}

		$i = $closeTagIndex + 1;
		while (isset($tokens[$i])) {
			if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === $variable) {
				return true;
			}
			$i++;
		}

		return false;
	}

	/**
	 * Checks the property token chain for existence.
	 *
	 * T_VARIABLE ..., content=`$this`
	 * T_OBJECT_OPERATOR ..., content=`->`
	 * T_STRING ..., content=`PropertyName`
	 * T_OBJECT_OPERATOR ..., content=`->`
	 *
	 * @param array<array<string, mixed>> $tokens
	 * @param int $closeTagIndex
	 * @param string $variable
	 *
	 * @return bool
	 */
	protected function propertyInUse(array $tokens, int $closeTagIndex, string $variable): bool {
		/** @var string $property */
		$property = substr($variable, 1);

		$i = $closeTagIndex + 1;
		while (isset($tokens[$i])) {
			if ($tokens[$i]['code'] !== T_VARIABLE || $tokens[$i]['content'] !== '$this') {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_OBJECT_OPERATOR) {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== $property) {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_OBJECT_OPERATOR) {
				$i++;

				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks the property token chain for existence.
	 *
	 * T_VARIABLE ..., content=`$this`
	 * T_OBJECT_OPERATOR ..., content=`->`
	 * T_STRING ..., content=`method`
	 * T_OPEN_PARENTHESIS ..., content=`(`
	 *
	 * @param array<array<string, mixed>> $tokens
	 * @param int $closeTagIndex
	 * @param string $method
	 *
	 * @return bool
	 */
	protected function methodInUse(array $tokens, int $closeTagIndex, string $method): bool {
		if (!preg_match('#^(\w+)\(#', $method, $matches)) {
			return false;
		}
		$method = $matches[1];

		$i = $closeTagIndex + 1;
		while (isset($tokens[$i])) {
			if ($tokens[$i]['code'] !== T_VARIABLE || $tokens[$i]['content'] !== '$this') {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_OBJECT_OPERATOR) {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== $method) {
				$i++;

				continue;
			}
			$i++;
			if (!isset($tokens[$i])) {
				break;
			}
			if ($tokens[$i]['code'] !== T_OPEN_PARENTHESIS) {
				$i++;

				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $lastTagIndexOfPreviousLine
	 *
	 * @return bool
	 */
	protected function needsNewLineInDocBlock(File $file, int $lastTagIndexOfPreviousLine): bool {
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
	 * @param int $index
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation>|array<string> $annotations
	 *
	 * @return string
	 */
	protected function addNewDocBlock(File $file, int $index, array $annotations) {
		$tokens = $file->getTokens();

		foreach ($annotations as $key => $annotation) {
			if (is_string($annotation)) {
				continue;
			}
			$annotations[$key] = (string)$annotation;
		}

		$helper = new DocBlockHelper(new View());
		$annotationString = $helper->classDescription('', '', $annotations);
		if (PHP_EOL !== "\n") {
			$annotationString = str_replace("\n", PHP_EOL, $annotationString);
		}

		$fixer = $this->getFixer($file);

		$docBlock = $annotationString . PHP_EOL;
		$fixer->replaceToken($index, $docBlock . $tokens[$index]['content']);

		$contents = $fixer->getContents();

		$this->_counter[static::COUNT_ADDED] = count($annotations);

		return $contents;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $docBlockCloseIndex
	 *
	 * @return bool
	 */
	protected function isInlineDocBlock(File $file, int $docBlockCloseIndex): bool {
		$tokens = $file->getTokens();

		$docBlockOpenIndex = $tokens[$docBlockCloseIndex]['comment_opener'];

		return $tokens[$docBlockCloseIndex]['line'] === $tokens[$docBlockOpenIndex]['line'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $docBlockCloseIndex
	 * @return bool
	 */
	protected function shouldSkip(File $file, int $docBlockCloseIndex): bool {
		$tokens = $file->getTokens();
		$docBlockOpenIndex = $tokens[$docBlockCloseIndex]['comment_opener'];

		for ($i = $docBlockOpenIndex + 1; $i < $docBlockCloseIndex; $i++) {
			if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
				continue;
			}
			if (mb_strtolower($tokens[$i]['content']) === '@inheritdoc') {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array<string> $usedModels
	 * @param string $content
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function getModelAnnotations(array $usedModels, string $content): array {
		$annotations = [];

		foreach ($usedModels as $usedModel) {
			$className = App::className($usedModel, 'Model/Table', 'Table');
			if (!$className) {
				$className = 'Cake\ORM\Table';
			}
			[, $name] = pluginSplit($usedModel);

			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $className, '$' . $name);
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
	 * @param object $object Instantiated object that we want the property off.
	 * @param string $name Property name to fetch.
	 *
	 * @return mixed Property value.
	 */
	protected function invokeProperty(&$object, string $name) {
		$reflection = new ReflectionClass(get_class($object));
		$property = $reflection->getProperty($name);
		$property->setAccessible(true);

		return $property->getValue($object);
	}

	/**
	 * @return void
	 */
	protected function report(): void {
		$out = [];

		$added = !empty($this->_counter[static::COUNT_ADDED]) ? $this->_counter[static::COUNT_ADDED] : 0;
		if ($added) {
			$out[] = $added . ' ' . ($added === 1 ? 'annotation' : 'annotations') . ' added';
		}
		$updated = !empty($this->_counter[static::COUNT_UPDATED]) ? $this->_counter[static::COUNT_UPDATED] : 0;
		if ($updated) {
			$out[] = $updated . ' ' . ($updated === 1 ? 'annotation' : 'annotations') . ' updated';
		}
		$removed = !empty($this->_counter[static::COUNT_REMOVED]) ? $this->_counter[static::COUNT_REMOVED] : 0;
		if ($removed) {
			$out[] = $removed . ' ' . ($removed === 1 ? 'annotation' : 'annotations') . ' removed';
		}
		$skipped = !empty($this->_counter[static::COUNT_SKIPPED]) ? $this->_counter[static::COUNT_SKIPPED] : 0;
		if ($skipped) {
			$out[] = $skipped . ' ' . ($skipped === 1 ? 'annotation' : 'annotations') . ' skipped';
		}

		if (!$out) {
			return;
		}

		$this->_io->success('   -> ' . implode(', ', $out) . '.');
	}

	/**
	 * @return void
	 */
	protected function reportSkipped(): void {
		$out = [];

		$skipped = !empty($this->_counter[static::COUNT_SKIPPED]) ? $this->_counter[static::COUNT_SKIPPED] : 0;
		if ($skipped) {
			$out[] = $skipped . ' ' . ($skipped === 1 ? 'annotation' : 'annotations') . ' skipped';
		}

		if (!$out) {
			return;
		}

		$this->_io->out('   -> ' . implode(', ', $out));
	}

	/**
	 * @return void
	 */
	protected function resetCounter(): void {
		$this->_counter = [
			static::COUNT_ADDED => 0,
			static::COUNT_UPDATED => 0,
			static::COUNT_REMOVED => 0,
			static::COUNT_SKIPPED => 0,
		];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $classOrTraitIndex
	 *
	 * @return int
	 */
	protected function beginningOfLine(File $file, $classOrTraitIndex) {
		$tokens = $file->getTokens();

		$line = $tokens[$classOrTraitIndex]['line'];
		$beginningOfLineIndex = $classOrTraitIndex;
		while ($tokens[$beginningOfLineIndex - 1]['line'] === $line) {
			$beginningOfLineIndex--;
		}

		return $beginningOfLineIndex;
	}

	/**
	 * @phpstan-param class-string<object> $className
	 *
	 * @param string $className
	 *
	 * @return bool
	 */
	protected function _isAbstract($className) {
		$reflection = new ReflectionClass($className);

		return $reflection->isAbstract();
	}

}
