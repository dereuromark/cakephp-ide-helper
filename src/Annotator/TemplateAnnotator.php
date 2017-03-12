<?php
namespace IdeHelper\Annotator;

use Bake\View\Helper\DocBlockHelper;
use Cake\Core\App;
use Cake\Utility\Inflector;
use Cake\View\View;

/**
 */
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

		$entityAnnotations = $this->getEntityAnnotations($content);
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
		if (!$annotations) {
			return false;
		}

		$helper = new DocBlockHelper(new View());

		$annotationString = $helper->classDescription('', '', $annotations);

		$file = $this->_getFile($path);
		$file->start($content);

		$tokens = $file->getTokens();

		$classIndex = $file->findNext(T_OPEN_TAG, 0);
		$needsPhpTag = true;
		if ($classIndex === 0 || $this->_isFirstContent($tokens, $classIndex)) {
			$needsPhpTag = false;
		}
		if ($needsPhpTag) {
			$annotationString = '<?php' . PHP_EOL . $annotationString . PHP_EOL . '?>';
		}

		$fixer = $this->_getFixer();
		$fixer->startFile($file);

		$docBlock = $annotationString . PHP_EOL;
		if ($needsPhpTag) {
			$fixer->addContentBefore(0, $docBlock);
		} else {
			$fixer->addContent($classIndex, $docBlock);
		}

		$contents = $fixer->getContents();

		$this->_storeFile($path, $contents);

		$this->_io->out('   * ' . count($annotations) . ' annotations added');

		return true;
	}

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	protected function _needsViewAnnotation($content) {
		if (preg_match('/\* \@var .+ \$this\b/', $content)) {
			return false;
		}

		if (preg_match('/\$this-\>/', $content)) {
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	protected function _getViewAnnotation() {
		$className = 'App\View\AppView';
		if (!class_exists($className)) {
			$className = 'Cake\View\View';
		}

		return '@var \\' . $className . ' $this';
	}

	/**
	 * @param array $tokens
	 * @param int $classIndex
	 *
	 * @return bool
	 */
	protected function _isFirstContent(array $tokens, $classIndex) {
		for ($i = $classIndex - 1; $i >= 0; $i--) {
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
	protected function getEntityAnnotations($content) {
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
		preg_match_all('/\$this-\>Form->create\(\$([a-z]+)\)/i', $content, $matches);
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

			$result[$entity] = '@var \\' . $className . ' $' . $entity;
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

			$result[$matches[1][$key]] = '@var \\' . $className . '[] $' . $matches[1][$key];
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

			$result[$entity] = '@var \\' . $className . ' $' . $entity;
		}

		return $result;
	}

}
