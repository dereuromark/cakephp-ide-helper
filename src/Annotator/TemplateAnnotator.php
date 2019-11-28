<?php

namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use IdeHelper\Annotator\Template\VariableExtractor;
use PHP_CodeSniffer\Files\File;
use RuntimeException;

class TemplateAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);

		$annotations = $this->_buildAnnotations($path, $content);

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $annotations
	 *
	 * @return bool
	 */
	protected function _annotate($path, $content, array $annotations) {
		if (!count($annotations)) {
			return false;
		}

		$file = $this->_getFile($path, $content);

		$phpOpenTagIndex = $file->findNext(T_OPEN_TAG, 0);
		if ($phpOpenTagIndex === false) {
			$phpOpenTagIndex = null;
		}

		$needsPhpTag = $phpOpenTagIndex === null || $this->_needsPhpTag($file, $phpOpenTagIndex);
		$docBlockCloseTagIndex = null;
		if ($needsPhpTag) {
			$phpOpenTagIndex = null;
		} else {
			$docBlockCloseTagIndex = $this->_findExistingDocBlock($file, $phpOpenTagIndex);
		}

		$this->_resetCounter();
		if ($docBlockCloseTagIndex && !$this->isInlineDocBlock($file, $docBlockCloseTagIndex)) {
			$newContent = $this->_appendToExistingDocBlock($file, $docBlockCloseTagIndex, $annotations);
		} else {
			$newContent = $this->_addNewTemplateDocBlock($file, $annotations, $phpOpenTagIndex, $docBlockCloseTagIndex);
		}

		$this->_displayDiff($content, $newContent);
		$this->_storeFile($path, $newContent);

		$this->_report();

		return true;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $phpOpenTagIndex
	 * @return int|null
	 */
	protected function _findExistingDocBlock(File $file, $phpOpenTagIndex) {
		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(T_WHITESPACE, $phpOpenTagIndex + 1, null, true);
		if ($tokens[$nextIndex]['type'] !== 'T_DOC_COMMENT_OPEN_TAG') {
			return null;
		}

		$commentCloseIndex = $tokens[$nextIndex]['comment_closer'];

		// Assume the first doc block is the license file doc block
		while ($index = $this->_findExistingDocBlock($file, $commentCloseIndex)) {
			$commentCloseIndex = $index;
		}

		return $commentCloseIndex;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $annotations
	 * @param int|null $phpOpenTagIndex
	 * @param int|null $docBlockCloseIndex
	 *
	 * @throws \RuntimeException
	 *
	 * @return string
	 */
	protected function _addNewTemplateDocBlock(File $file, array $annotations, $phpOpenTagIndex, $docBlockCloseIndex) {
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

		$fixer = $this->_getFixer($file);
		if ($phpOpenTagIndex === null) {
			$fixer->addContentBefore(0, $docBlock);
		} else {
			$fixer->addContent($phpOpenTagIndex, $docBlock);
		}

		$this->_counter[static::COUNT_ADDED] = count($annotations);

		if ($docBlockCloseIndex && $this->_isInlineDocBlockRedundant($file, $annotations, $docBlockCloseIndex)) {
			$tokens = $file->getTokens();
			$docBlockOpenIndex = $tokens[$docBlockCloseIndex]['comment_opener'];
			for ($i = $docBlockCloseIndex + 1; $i >= $docBlockOpenIndex; $i--) {
				$fixer->replaceToken($i, '');
			}

			$this->_counter[static::COUNT_ADDED]--;
		}

		$newContent = $fixer->getContents();

		return $newContent;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $phpOpenTagIndex
	 * @return bool
	 */
	protected function _needsPhpTag(File $file, $phpOpenTagIndex) {
		$needsPhpTag = true;

		$tokens = $file->getTokens();

		if ($phpOpenTagIndex === 0 || $phpOpenTagIndex > 0 && $this->_isFirstContent($tokens, $phpOpenTagIndex)) {
			$needsPhpTag = false;
		}
		if ($needsPhpTag) {
			return true;
		}

		$nextIndex = $file->findNext(T_WHITESPACE, $phpOpenTagIndex + 1, null, true);
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
	protected function _needsViewAnnotation($content) {
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
	protected function _getViewAnnotation() {
		$className = Configure::read('IdeHelper.viewClass') ?: 'App\View\AppView';
		if (!class_exists($className)) {
			$className = 'Cake\View\View';
		}

		/** @var \IdeHelper\Annotation\VariableAnnotation $annotation */
		$annotation = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className, '$this');

		return $annotation;
	}

	/**
	 * @param array $tokens
	 * @param int $phpOpenTagIndex
	 *
	 * @return bool
	 */
	protected function _isFirstContent(array $tokens, $phpOpenTagIndex) {
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
	 * @param array $variables
	 *
	 * @return array
	 */
	protected function _getEntityAnnotations($content, array $variables) {
		$loopEntityAnnotations = $this->_parseLoopEntities($content);
		$formEntityAnnotations = $this->_parseFormEntities($content);
		$entityAnnotations = $this->_parseEntities($content);

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
	 * @return array
	 */
	protected function _parseFormEntities($content) {
		preg_match_all('/\$this->Form->create\(\$(\w+)\W/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$result = [];

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
	 * @return array
	 */
	protected function _parseLoopEntities($content) {
		preg_match_all('/\bforeach \(\$([a-z]+) as \$([a-z]+)\)/i', $content, $matches);
		if (empty($matches[2])) {
			return [];
		}

		$result = [];

		foreach ($matches[2] as $key => $entity) {
			if (Inflector::pluralize($entity) !== $matches[1][$key]) {
				continue;
			}

			$entityName = Inflector::camelize(Inflector::underscore($entity));

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$resultKey = $matches[1][$key];
			$result[$resultKey] = AnnotationFactory::createOrFail(VariableAnnotation::TAG, '\\' . $className . '[]|\Cake\Collection\CollectionInterface', '$' . $matches[1][$key]);
			// We do not need the singular then
			$result[$entity] = null;
		}

		return $result;
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _parseEntities($content) {
		preg_match_all('/\$([a-z]+)->[a-z]+/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}
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
	 * @param \IdeHelper\Annotation\AbstractAnnotation[] $annotations
	 * @param int $docBlockCloseIndex
	 *
	 * @return bool
	 */
	protected function _isInlineDocBlockRedundant(File $file, array $annotations, $docBlockCloseIndex) {
		$existingAnnotations = $this->_parseExistingAnnotations($file, $docBlockCloseIndex);

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
	 * @return \IdeHelper\Annotation\AbstractAnnotation[]
	 */
	protected function _buildAnnotations($path, $content) {
		$annotations = [];

		$needsAnnotation = $this->_needsViewAnnotation($content);
		if ($needsAnnotation) {
			$annotations[] = $this->_getViewAnnotation();
		}

		$variables = $this->_getTemplateVariables($path, $content);

		$entityAnnotations = $this->_getEntityAnnotations($content, $variables);
		foreach ($variables as $name => $variable) {
			if ($variable['excludeReason'] || isset($entityAnnotations[$name])) {
				continue;
			}
			if (Configure::read('IdeHelper.autoCollect') === false) {
				continue;
			}

			$annotations[] = $this->_getVariableAnnotation($variable);
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
	 * @return array
	 */
	protected function _getTemplateVariables($path, $content) {
		$file = $this->_getFile($path, $content);

		$class = Configure::read('IdeHelper.variableExtractor') ?: VariableExtractor::class;
		/** @var \IdeHelper\Annotator\Template\VariableExtractor $extractor */
		$extractor = new $class();

		$variables = $extractor->extract($file);
		/** @var string[] $blacklist */
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
	 * @param array $variable
	 *
	 * @return \IdeHelper\Annotation\AbstractAnnotation
	 */
	protected function _getVariableAnnotation(array $variable) {
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

}
