<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Utility\Inflector;
use Cake\View\View;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\VariableAnnotation;
use PHP_CodeSniffer\Files\File;

class TemplateAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$content = file_get_contents($path);

		$annotations = [];
		$needsAnnotation = $this->_needsViewAnnotation($content);
		if ($needsAnnotation) {
			$annotations[] = $this->_getViewAnnotation();
		}

		$entityAnnotations = $this->_getEntityAnnotations($content);
		foreach ($entityAnnotations as $entityAnnotation) {
			if (preg_match('/' . preg_quote($entityAnnotation) . '/', $content)) {
				continue;
			}

			$annotations[] = $entityAnnotation;
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
		if (!count($annotations)) {
			return false;
		}

		$file = $this->_getFile($path, $content);

		$phpOpenTagIndex = $file->findNext(T_OPEN_TAG, 0);
		$needsPhpTag = $this->_needsPhpTag($file, $phpOpenTagIndex);

		$closeTagIndex = $this->_findExistingDocBlock($file, $phpOpenTagIndex, $needsPhpTag);
		if ($closeTagIndex) {
			$newContent = $this->_appendToExistingDocBlock($file, $closeTagIndex, $annotations);
		} else {
			$newContent = $this->_addNewTemplateDocBlock($file, $phpOpenTagIndex, $annotations, $needsPhpTag);
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
	 * @param int $phpOpenTagIndex
	 * @param bool $needsPhpTag
	 * @return int|null
	 */
	protected function _findExistingDocBlock(File $file, $phpOpenTagIndex, $needsPhpTag) {
		if ($needsPhpTag) {
			return null;
		}

		$tokens = $file->getTokens();

		$nextIndex = $file->findNext(T_WHITESPACE, $phpOpenTagIndex + 1, null, true);
		if ($tokens[$nextIndex]['type'] !== 'T_DOC_COMMENT_OPEN_TAG') {
			return null;
		}

		return $tokens[$nextIndex]['comment_closer'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param string $phpOpenTagIndex
	 * @param array $annotations
	 * @param bool $needsPhpTag
	 * @return string
	 */
	protected function _addNewTemplateDocBlock(File $file, $phpOpenTagIndex, array $annotations, $needsPhpTag) {
		$helper = new DocBlockHelper(new View());

		foreach ($annotations as $key => $annotation) {
			if (is_string($annotation)) {
				continue;
			}
			$annotations[$key] = (string)$annotation;
		}

		$annotationString = $helper->classDescription('', '', (array)$annotations);

		if ($needsPhpTag) {
			$annotationString = '<?php' . PHP_EOL . $annotationString . PHP_EOL . '?>';
		}

		$fixer = $this->_getFixer($file);

		$docBlock = $annotationString . PHP_EOL;
		if ($needsPhpTag) {
			$fixer->addContentBefore(0, $docBlock);
		} else {
			$fixer->addContent($phpOpenTagIndex, $docBlock);
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
		$viewAnnotation = $this->_getViewAnnotation()->build();
		if (preg_match('/' . preg_quote($viewAnnotation) . '\b/', $content)) {
			return false;
		}

		if (preg_match('/\$this-\>/', $content)) {
			return true;
		}

		return false;
	}

	/**
	 * @return \IdeHelper\Annotation\VariableAnnotation
	 */
	protected function _getViewAnnotation() {
		$className = 'App\View\AppView';
		if (!class_exists($className)) {
			$className = 'Cake\View\View';
		}

		/* @var \IdeHelper\Annotation\VariableAnnotation $annotation */
		$annotation = AnnotationFactory::create(VariableAnnotation::TAG, '\\' . $className, '$this');

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
	 *
	 * @return array
	 */
	protected function _getEntityAnnotations($content) {
		$loopEntityAnnotations = $this->_parseLoopEntities($content);
		$formEntityAnnotations = $this->_parseFormEntities($content);
		$entityAnnotations = $this->_parseEntities($content);

		$entityAnnotations = $loopEntityAnnotations + $formEntityAnnotations + $entityAnnotations;

		return $entityAnnotations;
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	protected function _parseFormEntities($content) {
		preg_match_all('/\$this-\>Form->create\(\$(\w+)\W/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$result = [];

		$entities = array_unique($matches[1]);
		foreach ($entities as $entity) {
			$entityName = Inflector::classify($entity);

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$result[$entity] = AnnotationFactory::create(VariableAnnotation::TAG, '\\' . $className, '$' . $entity);
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

			$entityName = Inflector::classify($entity);

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$result[$matches[1][$key]] = AnnotationFactory::create(VariableAnnotation::TAG, '\\' . $className . '[]|\Cake\Collection\CollectionInterface', '$' . $matches[1][$key]);
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
		preg_match_all('/\$([a-z]+)-\>[a-z]+/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}
		$variableNames = array_unique($matches[1]);

		$result = [];

		foreach ($variableNames as $entity) {
			if ($entity === 'this') {
				continue;
			}
			if (preg_match('/ as \$' . $entity . '\b/', $content)) {
				continue;
			}

			$entityName = Inflector::classify($entity);

			$className = App::className(($this->getConfig(static::CONFIG_PLUGIN) ? $this->getConfig(static::CONFIG_PLUGIN) . '.' : '') . $entityName, 'Model/Entity');
			if (!$className) {
				continue;
			}

			$result[$entity] = AnnotationFactory::create(VariableAnnotation::TAG, '\\' . $className, '$' . $entity);
		}

		return $result;
	}

}
