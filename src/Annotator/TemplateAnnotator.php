<?php

namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use IdeHelper\Annotator\Template\VariableExtractor;
use IdeHelper\Utility\App;
use IdeHelper\Utility\CollectionClass;
use IdeHelper\Utility\GenericString;
use PHP_CodeSniffer\Files\File;
use RuntimeException;

class TemplateAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		$annotations = $this->buildAnnotations($path, $content);

		return $this->annotateContent($path, $content, $annotations);
	}

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

		$file = $this->getFile($path, $content);

		$phpOpenTagIndex = $file->findNext(T_OPEN_TAG, 0);
		if ($phpOpenTagIndex === false) {
			$phpOpenTagIndex = null;
		}

		$needsPhpTag = $phpOpenTagIndex === null || $this->needsPhpTag($file, $phpOpenTagIndex);

		$phpOpenTagIndex = $this->checkForDeclareStatement($file, $phpOpenTagIndex);

		$docBlockCloseTagIndex = null;
		if ($needsPhpTag) {
			$phpOpenTagIndex = null;
		}
		if ($phpOpenTagIndex !== null) {
			$docBlockCloseTagIndex = $this->findExistingDocBlock($file, $phpOpenTagIndex);
		}

		$this->resetCounter();
		if ($docBlockCloseTagIndex && !$this->isInlineDocBlock($file, $docBlockCloseTagIndex)) {
			$newContent = $this->appendToExistingDocBlock($file, $docBlockCloseTagIndex, $annotations);
		} else {
			$newContent = $this->addNewTemplateDocBlock($file, $annotations, $phpOpenTagIndex, $docBlockCloseTagIndex);
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
	 * @param int $phpOpenTagIndex
	 * @return int|null
	 */
	protected function findExistingDocBlock(File $file, int $phpOpenTagIndex): ?int {
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(T_WHITESPACE, $phpOpenTagIndex + 1, null, true);
		if ($tokens[$nextIndex]['type'] !== 'T_DOC_COMMENT_OPEN_TAG') {
			return null;
		}

		$commentCloseIndex = $tokens[$nextIndex]['comment_closer'];

		$tagIndex = $file->findNext(T_DOC_COMMENT_TAG, $phpOpenTagIndex + 1, $commentCloseIndex);
		if (!$tagIndex || $tokens[$tagIndex]['content'] === '@var') {
			return $commentCloseIndex;
		}

		// Assume the first doc block is the license file doc block
		while ($closeIndex = $this->findExistingDocBlock($file, $commentCloseIndex)) {
			$line = $tokens[$closeIndex]['line'];
			$openIndex = $tokens[$closeIndex]['comment_opener'];
			$nextContentIndex = $file->findNext(T_WHITESPACE, $closeIndex + 1, null, true);
			// This must be an inline docblock, so we need to bail
			if (
				$nextContentIndex
				&& $tokens[$nextContentIndex]['line'] === $line + 1
				&& $tokens[$openIndex]['line'] === $tokens[$closeIndex]['line']
			) {
				break;
			}

			$commentCloseIndex = $closeIndex;
		}

		return $commentCloseIndex;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 * @param int|null $phpOpenTagIndex
	 * @param int|null $docBlockCloseIndex
	 *
	 * @throws \RuntimeException
	 *
	 * @return string
	 */
	protected function addNewTemplateDocBlock(File $file, array $annotations, ?int $phpOpenTagIndex, ?int $docBlockCloseIndex): string {
		$helper = new DocBlockHelper(new View());

		$annotationStrings = [];
		foreach ($annotations as $key => $annotation) {
			if (!is_object($annotation)) {
				throw new RuntimeException('Must be object: ' . print_r($annotation, true));
			}
			$annotationStrings[$key] = (string)$annotation;
		}

		$annotationString = $helper->classDescription('', '', $annotationStrings);
		if (PHP_EOL !== "\n") {
			$annotationString = str_replace("\n", PHP_EOL, $annotationString);
		}

		if ($phpOpenTagIndex === null) {
			$annotationString = '<?php' . PHP_EOL . $annotationString . PHP_EOL . '?>';
		}

		$docBlock = $annotationString . PHP_EOL;
		if (!$file->getTokens()) {
			$this->_counter[static::COUNT_ADDED] = count($annotations);

			return $docBlock;
		}

		$fixer = $this->getFixer($file);
		if ($phpOpenTagIndex === null) {
			$fixer->addContentBefore(0, $docBlock);
		} else {
			$fixer->addContent($phpOpenTagIndex, $docBlock);
		}

		$this->_counter[static::COUNT_ADDED] = count($annotations);

		if ($docBlockCloseIndex && $this->isInlineDocBlockRedundant($file, $annotations, $docBlockCloseIndex)) {
			$tokens = $file->getTokens();
			$docBlockOpenIndex = $tokens[$docBlockCloseIndex]['comment_opener'];
			for ($i = $docBlockCloseIndex + 1; $i >= $docBlockOpenIndex; $i--) {
				$fixer->replaceToken($i, '');
			}

			$this->_counter[static::COUNT_ADDED]--;
		}

		return $fixer->getContents();
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $phpOpenTagIndex
	 * @return bool
	 */
	protected function needsPhpTag(File $file, int $phpOpenTagIndex): bool {
		$needsPhpTag = true;

		$tokens = $file->getTokens();

		if ($phpOpenTagIndex === 0 || ($phpOpenTagIndex > 0 && $this->isFirstContent($tokens, $phpOpenTagIndex))) {
			$needsPhpTag = false;
		}
		if ($needsPhpTag) {
			return true;
		}

		$nextIndex = $file->findNext(T_WHITESPACE, $phpOpenTagIndex + 1, null, true);
		if ($tokens[$nextIndex]['code'] === T_DECLARE) {
			return false;
		}

		if ($tokens[$nextIndex]['line'] === $tokens[$phpOpenTagIndex]['line']) {
			return true;
		}

		return $needsPhpTag;
	}

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	protected function needsViewAnnotation(string $content): bool {
		if (Configure::read('IdeHelper.preemptive')) {
			return true;
 		}

		if (preg_match('/\$this->/', $content)) {
			return true;
 		}

		if (preg_match('/<\?/', $content)) {
			return true;
		}

 		return false;
	}

	/**
	 * @return \IdeHelper\Annotation\VariableAnnotation
	 */
	protected function getViewAnnotation() {
		$className = Configure::read('IdeHelper.viewClass');
		if (!$className) {
			$className = (Configure::read('App.namespace') ?: 'App') . '\View\AppView';
		}
		if (!class_exists($className)) {
			$className = 'Cake\View\View';
		}

		/** @var \IdeHelper\Annotation\VariableAnnotation $annotation */
		$annotation = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className, '$this');

		return $annotation;
	}

	/**
	 * @param array<array<string, mixed>> $tokens
	 * @param int $phpOpenTagIndex
	 *
	 * @return bool
	 */
	protected function isFirstContent(array $tokens, int $phpOpenTagIndex): bool {
		for ($i = $phpOpenTagIndex - 1; $i >= 0; $i--) {
			if ($tokens[$i]['type'] !== T_INLINE_HTML) {
				return false;
			}
			if (trim($tokens[$i]['content']) !== '') {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param string $content
	 * @param array<string, array<string, mixed>> $variables
	 *
	 * @return array<string, mixed>
	 */
	protected function getEntityAnnotations(string $content, array $variables): array {
		$loopEntityAnnotations = $this->parseLoopEntities($content);
		$formEntityAnnotations = $this->parseFormEntities($content);
		$entityAnnotations = $this->parseEntities($content);

		$entityAnnotations = $loopEntityAnnotations + $formEntityAnnotations + $entityAnnotations;

		foreach ($entityAnnotations as $name => $entityAnnotation) {
			if (!empty($variables[$name]) && $variables[$name]['excludeReason']) {
				unset($entityAnnotations[$name]);
			}
		}

		return $entityAnnotations;
	}

	/**
	 * @param string $content
	 *
	 * @return array<string, mixed>
	 */
	protected function parseFormEntities(string $content): array {
		preg_match_all('/\$this->Form->create\(\$(\w+)\W/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$result = [];

		/** @var array<string> $entities */
		$entities = array_unique($matches[1]);
		foreach ($entities as $entity) {
			$entityName = Inflector::camelize(Inflector::underscore($entity));

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$annotation = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className, '$' . $entity);

			$result[$entity] = $annotation;
		}

		return $result;
	}

	/**
	 * @param string $content
	 *
	 * @return array<string, mixed>
	 */
	protected function parseLoopEntities(string $content): array {
		preg_match_all('/\bforeach \(\$([a-z]+) as \$([a-z]+)\)/i', $content, $matches);
		if (empty($matches[2])) {
			return [];
		}

		$result = [];

		/** @var array<string> $entities */
		$entities = $matches[2];
		foreach ($entities as $key => $entity) {
			if (Inflector::pluralize($entity) !== $matches[1][$key]) {
				continue;
			}

			$entityName = Inflector::camelize(Inflector::underscore($entity));

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$resultKey = $matches[1][$key];
			$annotation = GenericString::generate('\\' . $className);
			if (Configure::read('IdeHelper.templateCollectionObject') !== false) {
				$collectionClass = CollectionClass::name('\\' . CollectionInterface::class);
				/** @var string|bool|null $collectionType */
				$collectionType = Configure::read('IdeHelper.templateCollectionObject');
				if (Configure::read('IdeHelper.objectAsGenerics') === true && $collectionType !== 'iterable') {
					$annotation .= '|' . GenericString::generate('\\' . $className, $collectionClass);
				} else {
					$annotation = GenericString::generate('\\' . $className, $collectionClass);
				}
			}

			$result[$resultKey] = AnnotationFactory::createOrFail(VariableAnnotation::TAG, $annotation, '$' . $matches[1][$key]);
			// We do not need the singular then
			$result[$entity] = null;
		}

		return $result;
	}

	/**
	 * @param string $content
	 *
	 * @return array<string, mixed>
	 */
	protected function parseEntities(string $content): array {
		preg_match_all('/\$([a-z]+)->[a-z]+/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}
		/** @var array<string> $variableNames */
		$variableNames = array_unique($matches[1]);

		$result = [];

		foreach ($variableNames as $entity) {
			if ($entity === 'this') {
				continue;
			}

			$entityName = Inflector::camelize(Inflector::underscore($entity));

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$result[$entity] = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className, '$' . $entity);
		}

		return $result;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param array<\IdeHelper\Annotation\AbstractAnnotation> $annotations
	 * @param int $docBlockCloseIndex
	 *
	 * @return bool
	 */
	protected function isInlineDocBlockRedundant(File $file, array $annotations, int $docBlockCloseIndex): bool {
		$existingAnnotations = $this->parseExistingAnnotations($file, $docBlockCloseIndex);

		foreach ($existingAnnotations as $existingAnnotation) {
			foreach ($annotations as $annotation) {
				if ($existingAnnotation->build() === $annotation->build()) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param string $path
	 * @param string $content
	 *
	 * @return array<\IdeHelper\Annotation\AbstractAnnotation>
	 */
	protected function buildAnnotations(string $path, string $content): array {
		$annotations = [];

		$needsAnnotation = $this->needsViewAnnotation($content);
		if ($needsAnnotation) {
			$annotations[] = $this->getViewAnnotation();
		}

		$variables = $this->getTemplateVariables($path, $content);

		$entityAnnotations = $this->getEntityAnnotations($content, $variables);
		foreach ($variables as $name => $variable) {
			if ($variable['excludeReason'] || isset($entityAnnotations[$name])) {
				continue;
			}
			if (Configure::read('IdeHelper.autoCollect') === false) {
				continue;
			}

			$annotations[] = $this->getVariableAnnotation($variable);
		}

		/** @var \IdeHelper\Annotation\AbstractAnnotation|null $entityAnnotation */
		foreach ($entityAnnotations as $entityAnnotation) {
			if (!$entityAnnotation) {
				continue;
			}
			$annotations[] = $entityAnnotation;
		}

		return $annotations;
	}

	/**
	 * Gets all template variables and a bit about their scope/context
	 * - type (if detected, e.g. string, object)
	 * - excludeReason (if detected as excludable, e.g. inside local assignment/loop)
	 *
	 * @param string $path
	 * @param string $content
	 *
	 * @return array<string, mixed>
	 */
	protected function getTemplateVariables($path, $content) {
		$file = $this->getFile($path, $content);

		$class = Configure::read('IdeHelper.variableExtractor') ?: VariableExtractor::class;
		/** @var \IdeHelper\Annotator\Template\VariableExtractor $extractor */
		$extractor = new $class();

		$variables = $extractor->extract($file);
		/** @var array<string> $blacklist */
		$blacklist = (array)Configure::read('IdeHelper.autoCollectBlacklist');
		foreach ($blacklist as $value) {
			if (strpos($value, '/') === false) {
				unset($variables[$value]);

				continue;
			}

			foreach ($variables as $name => $variable) {
				if (preg_match($value, $name)) {
					unset($variables[$name]);
				}
			}
		}

		return $variables;
	}

	/**
	 * @param array<string, mixed> $variable
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation
	 */
	protected function getVariableAnnotation(array $variable) {
		$defaultType = Configure::read('IdeHelper.autoCollect');
		$type = $variable['type'];
		if ($type === null) {
			$type = $defaultType;
		}

		if (is_callable($defaultType)) {
			$guessedType = $defaultType($variable);
			if ($guessedType) {
				$type = $guessedType;
			}
		}
		if (!$type || $type === true) {
			$type = 'mixed';
		}

		/** @var \IdeHelper\Annotation\VariableAnnotation $annotation */
		$annotation = AnnotationFactory::createOrFail(VariableAnnotation::TAG, $type, '$' . $variable['name']);
		$annotation->setGuessed(true);

		/** @return \IdeHelper\Annotator\AbstractAnnotator */
		return $annotation;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int|null $phpOpenTagIndex
	 *
	 * @return int|null
	 */
	protected function checkForDeclareStatement(File $file, ?int $phpOpenTagIndex): ?int {
		if ($phpOpenTagIndex === null) {
			return $phpOpenTagIndex;
		}

		$nextIndex = $file->findNext(T_DECLARE, $phpOpenTagIndex, $phpOpenTagIndex + 2);
		if (!$nextIndex) {
			return $phpOpenTagIndex;
		}

		$tokens = $file->getTokens();

		$lastIndexOfRow = $tokens[$nextIndex]['parenthesis_closer'];
		while (!empty($tokens[$lastIndexOfRow + 1]) && $tokens[$lastIndexOfRow + 1]['line'] === $tokens[$lastIndexOfRow]['line']) {
			$lastIndexOfRow++;
		}

		return $lastIndexOfRow;
	}

}
